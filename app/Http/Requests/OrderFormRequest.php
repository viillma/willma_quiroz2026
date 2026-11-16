<?php

namespace App\Http\Requests;

use App\Models\Order;
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
            'status' => ['required', Rule::in(array_keys(Order::STATUSES))],
            'notes' => ['nullable', 'string'],
            'delivery_date' => ['nullable', 'date'],
            'descuento' => ['nullable', 'numeric', 'min:0'],
            'adelanto' => ['nullable', 'numeric', 'min:0'],
            'forma_pago' => ['nullable', 'string', 'max:50'],
            'responsable' => ['nullable', 'string', 'max:100'],
            'servicios' => ['required', 'array', 'min:1'],
            'servicios.*.servicio_id' => ['required', 'exists:servicios,id'],
            'servicios.*.quantity' => ['required', 'integer', 'min:1'],
            'servicios.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
