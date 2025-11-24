<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    private const FACTURA = '01';
    private const BOLETA = '03';
    
    private string $apiUrl;
    private string $apiToken;
    private string $companyRuc;
    private string $companyRazonSocial;
    private string $serieFactura;
    private string $serieBoleta;

    public function __construct()
    {
        $this->apiUrl = config('invoicing.apis_peru.endpoint', 'https://facturacion.apisperu.com/api/v1/invoice/send');
        $this->apiToken = config('invoicing.apis_peru.token');
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
                'subtotal' => $order->total / 1.18, // Subtotal sin IGV
                'igv' => $order->total - ($order->total / 1.18), // IGV 18%
                'total' => $order->total,
                'status' => 'processing',
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
                'trace' => $e->getTraceAsString()
            ]);
            
            // If invoice was created, mark as error
            if (isset($invoice)) {
                $invoice->update([
                    'status' => 'error',
                    'error_message' => $e->getMessage()
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

        return str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
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
        foreach ($order->items as $item) {
            $precioSinIgv = floatval($item->price) / 1.18;
            $subtotal = $precioSinIgv * $item->quantity;
            $igv = $subtotal * 0.18;

            $details[] = [
                'codProducto' => 'PROD' . $item->product_id,
                'unidad' => 'NIU', // Unidad de medida
                'descripcion' => $item->product->name ?? 'Producto',
                'cantidad' => floatval($item->quantity),
                'mtoValorUnitario' => round($precioSinIgv, 2),
                'mtoValorVenta' => round($subtotal, 2),
                'mtoBaseIgv' => round($subtotal, 2),
                'porcentajeIgv' => 18,
                'igv' => round($igv, 2),
                'tipAfeIgv' => '10', // Gravado - Operación Onerosa
                'totalImpuestos' => round($igv, 2),
                'mtoPrecioUnitario' => round(floatval($item->price), 2)
            ];
        }

        // Get customer data
        $customerName = $order->user_id 
            ? $order->user->name 
            : trim($order->guest_name . ' ' . $order->guest_lastname);
        
        $customerEmail = $order->user_id ? $order->user->email : $order->guest_email;

        // Determine customer document type for API
        $clientTipoDoc = match($order->document_type) {
            'RUC' => '6',
            'DNI' => '1',
            'Pasaporte' => '7',
            'Carnet de Extranjeria' => '4',
            default => '1',
        };

        // Convert total to words (simplified version)
        $totalWords = $this->numberToWords($invoice->total);

        $payload = [
            'ublVersion' => '2.1',
            'tipoOperacion' => '0101',
            'tipoDoc' => $invoice->tipo_doc,
            'serie' => $invoice->serie,
            'correlativo' => $invoice->correlativo,
            'fechaEmision' => now()->format('Y-m-d\TH:i:sP'),
            'formaPago' => [
                'moneda' => 'PEN',
                'tipo' => 'Contado'
            ],
            'tipoMoneda' => 'PEN',
            'client' => [
                'tipoDoc' => $clientTipoDoc,
                'numDoc' => $order->document_number,
                'rznSocial' => $customerName,
                'address' => [
                    'direccion' => $order->shipping_address ?? ''
                ]
            ],
            'company' => [
                'ruc' => $this->companyRuc,
                'razonSocial' => $this->companyRazonSocial
            ],
            'mtoOperGravadas' => round(floatval($invoice->subtotal), 2),
            'mtoIGV' => round(floatval($invoice->igv), 2),
            'totalImpuestos' => round(floatval($invoice->igv), 2),
            'valorVenta' => round(floatval($invoice->subtotal), 2),
            'subTotal' => round(floatval($invoice->total), 2),
            'mtoImpVenta' => round(floatval($invoice->total), 2),
            'details' => $details,
            'legends' => [
                [
                    'code' => '1000',
                    'value' => $totalWords
                ]
            ]
        ];

        return $payload;
    }

    /**
     * Save invoice JSON locally
     */
    private function saveInvoiceJson(Invoice $invoice, array $payload): void
    {
        $filename = "invoices/{$invoice->serie}-{$invoice->correlativo}.json";
        
        // Save to public disk
        Storage::disk('public')->put($filename, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        // Update invoice status and URL
        $invoice->update([
            'status' => 'success',
            'api_response' => ['message' => 'JSON generado localmente'],
            'pdf_url' => Storage::url($filename), // We use pdf_url to store the JSON URL for now
            'xml_url' => null,
            'cdr_url' => null
        ]);
    }

    /**
     * Convert number to Spanish words (simplified version)
     */
    private function numberToWords(float $amount): string
    {
        $integerPart = floor($amount);
        $decimalPart = round(($amount - $integerPart) * 100);

        // This is a simplified version
        // For production, use a library like luecano/numero-a-letras
        return sprintf('SON %s CON %02d/100 SOLES', 
            $this->simpleNumberToWords($integerPart),
            $decimalPart
        );
    }

    /**
     * Simple number to words conversion (basic implementation)
     */
    private function simpleNumberToWords(int $number): string
    {
        // Basic implementation - for production use proper library
        $units = ['', 'UN', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE'];
        $tens = ['', '', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
        $hundreds = ['', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS', 'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS'];

        if ($number == 0) return 'CERO';
        if ($number == 100) return 'CIEN';
        
        $words = '';
        
        // Hundreds
        $h = floor($number / 100);
        if ($h > 0) {
            $words .= $hundreds[$h] . ' ';
            $number %= 100;
        }
        
        // Tens and units
        if ($number >= 20) {
            $t = floor($number / 10);
            $u = $number % 10;
            $words .= $tens[$t];
            if ($u > 0) $words .= ' Y ' . $units[$u];
        } else if ($number >= 10) {
            $special = ['DIEZ', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISEIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE'];
            $words .= $special[$number - 10];
        } else if ($number > 0) {
            $words .= $units[$number];
        }

        return trim($words);
    }
}
