<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', Rule::in(['efectivo', 'qr', 'transferencia', 'tarjeta', 'otro'])],
            'tipo_pago' => ['nullable', Rule::in(['adelanto', 'pago_parcial', 'pago_total'])],
            'reference' => ['nullable', 'string', 'max:255'],
        ];
    }
}
