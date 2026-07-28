<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DesignFileFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:ai,psd,pdf,png,jpg,jpeg,svg,eps', 'max:51200'],
            'comments' => ['nullable', 'string'],
        ];
    }
}
