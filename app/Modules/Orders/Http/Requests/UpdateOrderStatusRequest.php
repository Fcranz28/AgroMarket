<?php

namespace App\Modules\Orders\Http\Requests;

use App\Modules\Orders\Enums\OrderStatus;
use App\Modules\Orders\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends FormRequest
{
    /**
     * Only a farmer that sells every item of the order may change its status.
     */
    public function authorize(): bool
    {
        return $this->user()->can('updateStatus', $this->route('order'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(OrderStatus::class)],
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
            'status.required' => 'Selecciona un estado.',
            'status.in' => 'El estado seleccionado no es válido.',
        ];
    }
}
