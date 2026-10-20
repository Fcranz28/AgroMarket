<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesProductUnits;
use Illuminate\Foundation\Http\FormRequest;

class AdminProductRequest extends FormRequest
{
    use ValidatesProductUnits;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            ...$this->productRules(),
            'description' => 'nullable|string|max:2000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
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
            ...$this->productMessages(),
            'image.image' => 'El archivo debe ser una imagen.',
            'image.max' => 'La imagen puede pesar como máximo 2 MB.',
        ];
    }
}
