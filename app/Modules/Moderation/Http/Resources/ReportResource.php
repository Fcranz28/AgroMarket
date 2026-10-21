<?php

namespace App\Modules\Moderation\Http\Resources;

use App\Modules\Moderation\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Report detail for the admin panel.
 *
 * @mixin Report
 */
class ReportResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reason' => $this->reason,
            'description' => $this->description,
            'evidence' => $this->evidence ?? [],
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'admin_notes' => $this->admin_notes,
            'created_at' => $this->created_at?->toIso8601String(),
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'farmer' => $this->product->user?->only(['id', 'name']),
            ],
            'order' => [
                'id' => $this->order->id,
                'uuid' => $this->order->uuid,
            ],
        ];
    }
}
