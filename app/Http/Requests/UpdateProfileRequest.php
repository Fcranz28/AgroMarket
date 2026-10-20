<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateProfileRequest extends FormRequest
{
    /**
     * The profile and password forms share a page, so their errors are kept apart.
     *
     * @var string
     */
    protected $errorBag = 'updateProfile';

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
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user()->id)],
            'current_password' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'dni' => 'nullable|digits:8',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
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
            'name.required' => 'Ingresa tu nombre.',
            'email.required' => 'Ingresa tu correo electrónico.',
            'email.email' => 'El correo electrónico no es válido.',
            'email.unique' => 'Ese correo ya está registrado.',
            'dni.digits' => 'El DNI debe tener exactamente 8 dígitos.',
            'avatar.image' => 'La foto de perfil debe ser una imagen.',
            'avatar.max' => 'La foto de perfil puede pesar como máximo 2 MB.',
        ];
    }

    /**
     * Changing the e-mail (the login identifier) requires the current password.
     *
     * @return array<int, \Closure(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $user = $this->user();

                if (strcasecmp((string) $this->input('email'), $user->email) === 0) {
                    return;
                }

                if (! Hash::check((string) $this->input('current_password'), $user->password)) {
                    $validator->errors()->add('current_password', 'Ingresa tu contraseña actual para cambiar el correo.');
                }
            },
        ];
    }
}
