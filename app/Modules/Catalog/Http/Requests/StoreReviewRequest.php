<?php

namespace App\Modules\Catalog\Http\Requests;

use App\Modules\Catalog\Contracts\PurchaseHistory;
use App\Modules\Catalog\Models\Product;
use App\Modules\Catalog\Models\Review;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rating.required' => 'Selecciona una calificación.',
            'rating.min' => 'La calificación debe estar entre 1 y 5.',
            'rating.max' => 'La calificación debe estar entre 1 y 5.',
            'comment.max' => 'El comentario puede tener como máximo 1000 caracteres.',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.max' => 'La imagen puede pesar como máximo 2 MB.',
        ];
    }

    /**
     * Only buyers with a paid order of the product may review it, once, and never their own product.
     *
     * @return array<int, \Closure(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                /** @var Product $product */
                $product = $this->route('product');
                $user = $this->user();

                if ($product->user_id === $user->id) {
                    $validator->errors()->add('rating', 'No puedes reseñar tu propio producto.');

                    return;
                }

                if (! app(PurchaseHistory::class)->hasPaidPurchase($user, $product)) {
                    $validator->errors()->add('rating', 'Solo puedes reseñar productos que hayas comprado.');

                    return;
                }

                if (Review::where('user_id', $user->id)->where('product_id', $product->id)->exists()) {
                    $validator->errors()->add('rating', 'Ya dejaste una reseña para este producto.');
                }
            },
        ];
    }
}
