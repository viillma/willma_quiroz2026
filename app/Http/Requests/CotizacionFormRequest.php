<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CotizacionFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'fecha' => ['required', 'date'],
            'fecha_validez' => ['nullable', 'date', 'after_or_equal:fecha'],
            'descuento' => ['nullable', 'numeric', 'min:0'],
            'observaciones' => ['nullable', 'string'],
            'condiciones' => ['nullable', 'string'],
            'estado' => ['required', Rule::in(['borrador', 'enviada', 'aceptada', 'rechazada', 'vencida'])],
            'servicios' => ['required', 'array', 'min:1'],
            'servicios.*.servicio_id' => ['required', 'exists:servicios,id'],
            'servicios.*.quantity' => ['required', 'integer', 'min:1'],
            'servicios.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
