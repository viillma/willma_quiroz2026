<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactLead extends Model
{
    protected $fillable = [
        'nombre',
        'telefono',
        'correo',
        'servicio',
        'mensaje',
        'estado',
    ];

    protected function casts(): array
    {
        return [];
    }
}
