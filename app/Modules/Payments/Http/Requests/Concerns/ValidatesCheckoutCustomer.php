<?php

namespace App\Modules\Payments\Http\Requests\Concerns;

/**
 * Buyer and shipping data asked at checkout, shared by the JSON API and the Livewire
 * checkout so both validate the same way.
 */
trait ValidatesCheckoutCustomer
{
    public const DOCUMENT_TYPES = [
        'DNI' => 'DNI',
        'RUC' => 'RUC',
        'Pasaporte' => 'Pasaporte',
        'Carnet de Extranjeria' => 'Carnet de Extranjería',
    ];

    /**
     * @return array<string, string>
     */
    protected function customerRules(): array
    {
        return [
            'shipping_address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'guest_name' => 'required|string|max:100',
            'guest_lastname' => 'required|string|max:100',
            'guest_email' => 'required|email|max:255',
            'document_type' => 'required|in:'.implode(',', array_keys(self::DOCUMENT_TYPES)),
            'document_number' => 'required|string|alpha_num|max:20',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function customerMessages(): array
    {
        return [
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
