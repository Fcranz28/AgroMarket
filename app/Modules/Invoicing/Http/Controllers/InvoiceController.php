<?php

namespace App\Modules\Invoicing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Invoicing\Models\Invoice;
use App\Modules\Orders\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use OpenApi\Attributes as OA;

class InvoiceController extends Controller
{
    /**
     * Download invoice PDF
     */
    public function download(Invoice $invoice): Response|RedirectResponse
    {
        $this->authorize('view', $invoice);

        return $this->pdfResponse($invoice);
    }

    /**
     * Download a guest order's invoice through a signed URL.
     */
    public function guestDownload(Invoice $invoice): Response|RedirectResponse
    {
        abort_if($invoice->order->user_id !== null, 403);

        return $this->pdfResponse($invoice);
    }

    /**
     * Guest receipt page by order (signed URL returned right after paying). The receipt is
     * issued by a queue worker: until it exists the page reloads itself, then it shows a
     * signed download link.
     */
    public function guestReceipt(Order $order): Response
    {
        abort_if($order->user_id !== null, 403);

        $invoice = $order->invoice;
        $isReady = $invoice && ! $invoice->isPending();

        return response()->view('invoices.guest', [
            'downloadUrl' => $isReady ? URL::temporarySignedRoute('invoice.guest-download', now()->addDay(), $invoice) : null,
        ], $isReady ? 200 : 202);
    }

    private function pdfResponse(Invoice $invoice): Response|RedirectResponse
    {
        $path = $invoice->jsonPath();

        if (Storage::disk('local')->exists($path)) {
            $data = json_decode(Storage::disk('local')->get($path), true);

            return Pdf::loadView('invoices.pdf', compact('data'))
                ->download("{$invoice->serie}-{$invoice->correlativo}.pdf");
        }

        if ($invoice->status === 'error') {
            return back()->with('error', 'El comprobante no pudo ser generado. Contáctanos para emitirlo nuevamente.');
        }

        abort(404, 'Documento no disponible');
    }

    /**
     * Get invoice data as JSON (for display in dashboards)
     */
    #[OA\Get(
        path: '/factura/{invoice}',
        summary: 'Datos de una factura',
        description: 'Visible para el dueño del pedido, los agricultores con productos en él y los admins.',
        security: [['session' => []]],
        tags: ['Cliente'],
        parameters: [new OA\Parameter(name: 'invoice', in: 'path', required: true, description: 'UUID de la factura', schema: new OA\Schema(type: 'string', format: 'uuid'))],
        responses: [
            new OA\Response(response: 200, description: 'Factura y pedido', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'invoice', type: 'object', properties: [
                    new OA\Property(property: 'uuid', type: 'string', format: 'uuid'),
                    new OA\Property(property: 'tipo_doc', type: 'string', enum: ['01', '03'], description: '01 = Factura, 03 = Boleta'),
                    new OA\Property(property: 'numero_completo', type: 'string', example: 'B001-000123'),
                    new OA\Property(property: 'subtotal', type: 'string', example: '100.00'),
                    new OA\Property(property: 'igv', type: 'string', example: '18.00'),
                    new OA\Property(property: 'total', type: 'string', example: '118.00'),
                    new OA\Property(property: 'status', type: 'string', enum: ['pending', 'processing', 'success', 'error']),
                ]),
                new OA\Property(property: 'order', type: 'object'),
            ])),
            new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
            new OA\Response(ref: '#/components/responses/Forbidden', response: 403),
            new OA\Response(ref: '#/components/responses/NotFound', response: 404),
        ],
    )]
    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        return response()->json([
            'invoice' => $invoice,
            'order' => $invoice->order,
        ]);
    }
}
