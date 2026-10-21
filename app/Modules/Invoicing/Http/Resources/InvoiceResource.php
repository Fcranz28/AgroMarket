<?php

namespace App\Modules\Invoicing\Http\Resources;

use App\Modules\Invoicing\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Invoice data for dashboards. The raw request/response sent to the tax provider (which
 * include the customer's document) and the buyer's personal data are not exposed.
 *
 * @mixin Invoice
 */
class InvoiceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'invoice' => [
                'uuid' => $this->uuid,
                'tipo_doc' => $this->tipo_doc,
                'tipo_nombre' => $this->tipo_nombre,
                'numero_completo' => $this->numero_completo,
                'subtotal' => $this->subtotal,
                'igv' => $this->igv,
                'total' => $this->total,
                'status' => $this->status->value,
                'created_at' => $this->created_at?->toIso8601String(),
            ],
            'order' => [
                'uuid' => $this->order->uuid,
                'total' => $this->order->total,
                'status' => $this->order->status->value,
                'payment_status' => $this->order->payment_status->value,
                'created_at' => $this->order->created_at?->toIso8601String(),
            ],
        ];
    }
}
