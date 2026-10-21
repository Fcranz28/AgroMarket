<?php

namespace App\Modules\Catalog\Http\Resources;

use App\Modules\Catalog\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Product
 */
class SearchProductResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'price' => $this->price,
            'unit' => $this->unit ?? 'kg',
            'image_path' => $this->image_path ? asset('storage/'.$this->image_path) : null,
            'image_url' => $this->image_url,
            'category_name' => $this->whenLoaded('category', fn () => $this->category?->name),
        ];
    }
}
