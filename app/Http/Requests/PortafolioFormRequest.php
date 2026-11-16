<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PortafolioFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'servicio_id' => ['nullable', 'exists:servicios,id'],
            'categoria' => ['nullable', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string'],
            'fecha' => ['nullable', 'date'],
            'publicar' => ['nullable', 'boolean'],
            'imagen_principal' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:10240'],
            'imagenes_adicionales' => ['nullable', 'array'],
            'imagenes_adicionales.*' => ['image', 'mimes:png,jpg,jpeg,webp', 'max:10240'],
        ];
    }
}
