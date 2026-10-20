<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesProductUnits;
use Illuminate\Foundation\Http\FormRequest;

class FarmerProductRequest extends FormRequest
{
    use ValidatesProductUnits;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user->isAdmin() || $user->isVerified();
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
            'image' => $this->isMethod('post') ? 'required|array|min:1|max:8' : 'nullable|array|max:8',
            'image.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'integer',
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
            'image.required' => 'Sube al menos una imagen del producto.',
            'image.max' => 'Puedes subir como máximo 8 imágenes.',
            'image.*.image' => 'Cada archivo debe ser una imagen.',
            'image.*.mimes' => 'Las imágenes deben ser JPG, PNG o WEBP.',
            'image.*.max' => 'Cada imagen puede pesar como máximo 2 MB.',
        ];
    }

    /**
     * Ignore empty file inputs sent by the browser before validating.
     */
    protected function prepareForValidation(): void
    {
        $images = array_values(array_filter(
            (array) $this->file('image', []),
            fn ($file) => $file !== null && $file->isValid(),
        ));

        $this->files->set('image', $images);
    }
}
