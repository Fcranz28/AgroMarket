<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

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
    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        return response()->json([
            'invoice' => $invoice,
            'order' => $invoice->order,
        ]);
    }
}
