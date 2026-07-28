<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServicioFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'precio' => ['required', 'numeric', 'min:0'],
            'tiempo_estimado' => ['nullable', 'integer', 'min:1'],
            'imagen' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:512'],
            'estado' => ['required', 'in:activo,inactivo'],
        ];
    }
}
