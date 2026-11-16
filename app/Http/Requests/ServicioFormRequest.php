<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'slug' => ['nullable', 'string', 'max:255'],
            'codigo' => ['nullable', 'string', 'max:50'],
            'categoria' => ['nullable', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string'],
            'precio' => ['required', 'numeric', 'min:0'],
            'costo_estimado' => ['nullable', 'numeric', 'min:0'],
            'unidad' => ['nullable', 'string', 'max:50'],
            'tiempo_estimado' => ['nullable', 'integer', 'min:1'],
            'imagen' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'estado' => ['required', Rule::in(['activo', 'inactivo'])],
            'publicado' => ['sometimes', 'boolean'],
            'destacado' => ['sometimes', 'boolean'],
            'orden' => ['nullable', 'integer', 'min:0'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'caracteristicas' => ['nullable', 'array'],
            'caracteristicas.*' => ['string', 'max:255'],
            'incluye' => ['nullable', 'array'],
            'incluye.*' => ['string', 'max:255'],
        ];
    }
}
