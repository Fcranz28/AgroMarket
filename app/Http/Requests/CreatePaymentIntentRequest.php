<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentIntentRequest extends FormRequest
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
            'cart' => 'required|array|min:1|max:50',
            'cart.*.id' => 'required|integer|exists:products,id',
            'cart.*.cantidad' => 'required|integer|min:1|max:1000',
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
            'cart.required' => 'Tu carrito está vacío.',
            'cart.min' => 'Tu carrito está vacío.',
            'cart.max' => 'Tu carrito tiene demasiados productos.',
            'cart.*.id.exists' => 'Uno de los productos de tu carrito ya no está disponible.',
            'cart.*.cantidad.min' => 'La cantidad mínima por producto es 1.',
            'cart.*.cantidad.max' => 'La cantidad máxima por producto es 1000.',
        ];
    }
}
