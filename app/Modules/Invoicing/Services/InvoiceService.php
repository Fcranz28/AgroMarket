<?php

namespace App\Modules\Invoicing\Services;

use App\Modules\Invoicing\Enums\InvoiceStatus;
use App\Modules\Invoicing\Models\Invoice;
use App\Modules\Invoicing\Support\AmountInWords;
use App\Modules\Orders\Models\Order;
use App\Modules\Shared\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    private const FACTURA = '01';

    private const BOLETA = '03';

    private string $companyRuc;

    private string $companyRazonSocial;

    private string $serieFactura;

    private string $serieBoleta;

    public function __construct()
    {
        $this->companyRuc = config('invoicing.company.ruc');
        $this->companyRazonSocial = config('invoicing.company.razon_social');
        $this->serieFactura = config('invoicing.series.factura', 'F001');
        $this->serieBoleta = config('invoicing.series.boleta', 'B001');
    }

    /**
     * Generate electronic invoice for an order
     */
    public function generateInvoice(Order $order): Invoice
    {
        try {
            // Start database transaction
            DB::beginTransaction();

            // Determine invoice type and series
            [$tipoDoc, $serie] = $this->determineInvoiceType($order);

            // Get next correlativo number
            $correlativo = $this->getNextCorrelativo($serie);

            // Create invoice record (initially pending)
            $invoice = Invoice::create([
                'order_id' => $order->id,
                'tipo_doc' => $tipoDoc,
                'serie' => $serie,
                'correlativo' => $correlativo,
                'numero_completo' => "{$serie}-{$correlativo}",
                'subtotal' => Money::fromDecimal($order->total)->withoutIgv()->toDecimal(),
                'igv' => Money::fromDecimal($order->total)->igv()->toDecimal(),
                'total' => Money::fromDecimal($order->total)->toDecimal(),
                'status' => InvoiceStatus::Processing,
            ]);

            DB::commit();

            // Build and send payload to APIs Peru
            $payload = $this->buildInvoicePayload($order, $invoice);
            $invoice->api_request = $payload;
            $invoice->save();

            // Save JSON locally instead of sending to API
            $this->saveInvoiceJson($invoice, $payload);

            return $invoice->fresh();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Invoice generation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // If invoice was created, mark as error
            if (isset($invoice)) {
                $invoice->update([
                    'status' => InvoiceStatus::Error,
                    'error_message' => $e->getMessage(),
                ]);
            }

            throw $e;
        }
    }

    /**
     * Determine invoice type based on document type
     */
    private function determineInvoiceType(Order $order): array
    {
        $documentType = $order->document_type;
        $documentNumber = $order->document_number;

        // RUC (11 digits starting with 20) = Factura
        if ($documentType === 'RUC' || (strlen($documentNumber) === 11 && str_starts_with($documentNumber, '20'))) {
            return [self::FACTURA, $this->serieFactura];
        }

        // DNI or any other = Boleta
        return [self::BOLETA, $this->serieBoleta];
    }

    /**
     * Get next sequential correlativo number for a series
     */
    private function getNextCorrelativo(string $serie): string
    {
        $lastInvoice = Invoice::where('serie', $serie)
            ->orderBy('correlativo', 'desc')
            ->lockForUpdate()
            ->first();

        $nextNumber = $lastInvoice ? (intval($lastInvoice->correlativo) + 1) : 1;

        return str_pad((string) $nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Build invoice payload for APIs Peru
     */
    private function buildInvoicePayload(Order $order, Invoice $invoice): array
    {
        // Load order items
        $order->load('items.product');

        // Build details array
        $details = [];
        $totalBase = Money::zero();
        $totalIgv = Money::zero();

        foreach ($order->items as $item) {
            // Los precios publicados incluyen IGV: se desglosa la base a partir del total de la línea
            $unitPrice = Money::fromDecimal($item->price);
            $lineTotal = $unitPrice->multiply($item->quantity);
            $lineBase = $lineTotal->withoutIgv();
            $lineIgv = $lineTotal->igv();

            $totalBase = $totalBase->add($lineBase);
            $totalIgv = $totalIgv->add($lineIgv);

            $details[] = [
                'codProducto' => 'PROD'.$item->product_id,
                'unidad' => 'NIU', // Unidad de medida
                'descripcion' => trim(($item->product->name ?? 'Producto').($item->unit ? " ({$item->unit})" : '')),
                'cantidad' => (float) $item->quantity,
                'mtoValorUnitario' => $unitPrice->withoutIgv()->toFloat(),
                'mtoValorVenta' => $lineBase->toFloat(),
                'mtoBaseIgv' => $lineBase->toFloat(),
                'porcentajeIgv' => Money::IGV_RATE_PERCENT,
                'igv' => $lineIgv->toFloat(),
                'tipAfeIgv' => '10', // Gravado - Operación Onerosa
                'totalImpuestos' => $lineIgv->toFloat(),
                'mtoPrecioUnitario' => $unitPrice->toFloat(),
            ];
        }

        $totalWithIgv = $totalBase->add($totalIgv);

        // Get customer data
        $customerName = $order->user_id
            ? $order->user->name
            : trim($order->guest_name.' '.$order->guest_lastname);

        $customerEmail = $order->user_id ? $order->user->email : $order->guest_email;

        // Determine customer document type for API
        $clientTipoDoc = match ($order->document_type) {
            'RUC' => '6',
            'DNI' => '1',
            'Pasaporte' => '7',
            'Carnet de Extranjeria' => '4',
            default => '1',
        };

        $totalWords = AmountInWords::soles($totalWithIgv->toFloat());

        $payload = [
            'ublVersion' => '2.1',
            'tipoOperacion' => '0101',
            'tipoDoc' => $invoice->tipo_doc,
            'serie' => $invoice->serie,
            'correlativo' => $invoice->correlativo,
            'fechaEmision' => now()->format('Y-m-d\TH:i:sP'),
            'formaPago' => [
                'moneda' => 'PEN',
                'tipo' => 'Contado',
            ],
            'tipoMoneda' => 'PEN',
            'client' => [
                'tipoDoc' => $clientTipoDoc,
                'numDoc' => $order->document_number,
                'rznSocial' => $customerName,
                'address' => [
                    'direccion' => $order->shipping_address ?? '',
                ],
            ],
            'company' => [
                'ruc' => $this->companyRuc,
                'razonSocial' => $this->companyRazonSocial,
            ],
            'mtoOperGravadas' => $totalBase->toFloat(),
            'mtoIGV' => $totalIgv->toFloat(),
            'totalImpuestos' => $totalIgv->toFloat(),
            'valorVenta' => $totalBase->toFloat(),
            'subTotal' => $totalWithIgv->toFloat(),
            'mtoImpVenta' => $totalWithIgv->toFloat(),
            'details' => $details,
            'legends' => [
                [
                    'code' => '1000',
                    'value' => $totalWords,
                ],
            ],
        ];

        return $payload;
    }

    /**
     * Save invoice JSON locally
     */
    private function saveInvoiceJson(Invoice $invoice, array $payload): void
    {
        Storage::disk('local')->put($invoice->jsonPath(), json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $invoice->update([
            'status' => InvoiceStatus::Success,
            'api_response' => ['message' => 'JSON generado localmente'],
            'pdf_url' => null,
            'xml_url' => null,
            'cdr_url' => null,
        ]);
    }
}
