<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreReportRequest extends FormRequest
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
            'order_id' => 'required|integer',
            'product_id' => 'required|integer',
            'reason' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'evidence' => 'nullable|array|max:5',
            'evidence.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
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
            'reason.required' => 'Indica el motivo del reporte.',
            'description.required' => 'Describe el problema.',
            'evidence.max' => 'Puedes adjuntar como máximo 5 imágenes.',
            'evidence.*.image' => 'Las evidencias deben ser imágenes.',
            'evidence.*.max' => 'Cada imagen puede pesar como máximo 2 MB.',
        ];
    }

    /**
     * The order must belong to the user and contain the reported product.
     *
     * @return array<int, \Closure(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $ownsOrderWithProduct = Order::whereKey($this->integer('order_id'))
                    ->where('user_id', $this->user()->id)
                    ->whereHas('items', fn ($query) => $query->where('product_id', $this->integer('product_id')))
                    ->exists();

                if (! $ownsOrderWithProduct) {
                    $validator->errors()->add('order_id', 'Solo puedes reportar productos de tus propios pedidos.');
                }
            },
        ];
    }
}
