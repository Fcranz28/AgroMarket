<?php

namespace App\Modules\Identity\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FirebaseAuthRequest extends FormRequest
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
            'idToken' => 'required|string|max:4096',
            'isRegistering' => 'sometimes|boolean',
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
            'idToken.required' => 'Falta el token de autenticación.',
            'idToken.string' => 'El token de autenticación no es válido.',
            'idToken.max' => 'El token de autenticación no es válido.',
            'isRegistering.boolean' => 'El indicador de registro no es válido.',
        ];
    }
}
