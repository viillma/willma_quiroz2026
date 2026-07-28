<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'designer_id' => ['nullable', 'exists:users,id'],
            'status' => ['required', Rule::in(['pendiente', 'en_diseno', 'en_revision', 'aprobado', 'entregado'])],
            'notes' => ['nullable', 'string'],
            'delivery_date' => ['nullable', 'date'],
            'servicios' => ['required', 'array', 'min:1'],
            'servicios.*.servicio_id' => ['required', 'exists:servicios,id'],
            'servicios.*.quantity' => ['required', 'integer', 'min:1'],
            'servicios.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
