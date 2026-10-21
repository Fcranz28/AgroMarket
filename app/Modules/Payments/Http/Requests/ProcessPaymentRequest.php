<?php

namespace App\Modules\Payments\Http\Requests;

use App\Modules\Payments\Http\Requests\Concerns\ValidatesCheckoutCustomer;

class ProcessPaymentRequest extends CreatePaymentIntentRequest
{
    use ValidatesCheckoutCustomer;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'payment_intent_id' => 'required|string|starts_with:pi_|max:255',
            ...$this->customerRules(),
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
            ...parent::messages(),
            'payment_intent_id.*' => 'El identificador de pago no es válido.',
            ...$this->customerMessages(),
        ];
    }
}
