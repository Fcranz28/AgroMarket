<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FarmerVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isFarmer();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'face_photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'dni_front' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'dni_back' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
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
            '*.required' => 'Debes subir las tres fotos para verificar tu cuenta.',
            '*.image' => 'El archivo debe ser una imagen.',
            '*.mimes' => 'La imagen debe ser JPG, PNG o WEBP.',
            '*.max' => 'Cada imagen puede pesar como máximo 5 MB.',
        ];
    }
}
