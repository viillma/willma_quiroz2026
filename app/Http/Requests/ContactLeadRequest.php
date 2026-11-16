<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'correo' => ['nullable', 'email', 'max:255'],
            'servicio' => ['nullable', 'string', 'max:255'],
            'mensaje' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'Cuéntanos tu nombre.',
            'mensaje.required' => 'Escribe un mensaje.',
            'mensaje.min' => 'El mensaje debe tener al menos :min caracteres.',
        ];
    }
}
