<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    /**
     * Download invoice PDF
     */
    public function download(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        // If we have a local JSON file (stored in pdf_url for now)
        if ($invoice->pdf_url && str_contains($invoice->pdf_url, '.json')) {
            // Convert storage URL to path
            $path = str_replace('/storage', 'public', parse_url($invoice->pdf_url, PHP_URL_PATH));
            
            if (Storage::exists($path)) {
                return Storage::download($path, "{$invoice->serie}-{$invoice->correlativo}.json");
            }
        }

        // If we have a PDF URL from APIs Peru (legacy)
        if ($invoice->pdf_url) {
            return redirect($invoice->pdf_url);
        }

        // Check for specific statuses
        if ($invoice->status === 'error') {
            return back()->with('error', 'La factura no pudo ser generada. Error: ' . $invoice->error_message);
        }

        abort(404, 'Documento no disponible');
    }

    /**
     * View invoice details
     */
    public function view(Invoice $invoice)
    {
        // Verify user can access this invoice
        $this->authorize('view', $invoice);

        $invoice->load('order.items.product');

        return view('invoices.view', compact('invoice'));
    }

    /**
     * Get invoice data as JSON (for display in dashboards)
     */
    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        return response()->json([
            'invoice' => $invoice,
            'order' => $invoice->order
        ]);
    }
}
