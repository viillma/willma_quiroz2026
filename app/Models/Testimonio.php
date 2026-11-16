<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Testimonio extends Model
{
    protected $table = 'testimonios';

    protected $fillable = [
        'nombre',
        'texto',
        'calificacion',
        'servicio',
        'foto',
        'publicado',
        'orden',
    ];

    protected $appends = ['foto_url'];

    protected function casts(): array
    {
        return [
            'calificacion' => 'integer',
            'publicado' => 'boolean',
        ];
    }

    public function getFotoUrlAttribute(): ?string
    {
        if (! $this->foto) {
            return null;
        }

        if (filter_var($this->foto, FILTER_VALIDATE_URL)) {
            return $this->foto;
        }

        return Storage::disk('public')->url($this->foto);
    }

    public function scopePublicados($query)
    {
        return $query->where('publicado', true);
    }
}
