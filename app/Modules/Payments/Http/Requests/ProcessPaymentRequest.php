<?php

namespace App\Modules\Payments\Http\Requests;

class ProcessPaymentRequest extends CreatePaymentIntentRequest
{
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
            'shipping_address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'guest_name' => 'required|string|max:100',
            'guest_lastname' => 'required|string|max:100',
            'guest_email' => 'required|email|max:255',
            'document_type' => 'required|in:DNI,RUC,Pasaporte,Carnet de Extranjeria',
            'document_number' => 'required|string|alpha_num|max:20',
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
            'shipping_address.required' => 'Ingresa una dirección de envío.',
            'phone.required' => 'Ingresa un teléfono de contacto.',
            'guest_name.required' => 'Ingresa tus nombres.',
            'guest_lastname.required' => 'Ingresa tus apellidos.',
            'guest_email.required' => 'Ingresa tu correo electrónico.',
            'guest_email.email' => 'El correo electrónico no es válido.',
            'document_type.in' => 'El tipo de documento no es válido.',
            'document_number.required' => 'Ingresa tu número de documento.',
            'document_number.alpha_num' => 'El número de documento solo puede contener letras y números.',
        ];
    }
}
