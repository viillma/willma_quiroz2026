<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CatalogoDiseno extends Model
{
    protected $table = 'catalog_disenos';

    protected $fillable = [
        'imagen',
        'nombre',
        'categoria',
        'descripcion',
        'precio',
        'precio_anterior',
        'estado',
        'publicado',
        'destacado',
        'orden',
    ];

    protected $appends = ['imagen_url'];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'precio_anterior' => 'decimal:2',
            'publicado' => 'boolean',
            'destacado' => 'boolean',
        ];
    }

    public function scopePublicados($query)
    {
        return $query->where('publicado', true);
    }

    public function scopeDestacados($query)
    {
        return $query->where('destacado', true);
    }

    public function getImagenUrlAttribute(): ?string
    {
        if (! $this->imagen) {
            return null;
        }

        if (filter_var($this->imagen, FILTER_VALIDATE_URL)) {
            return $this->imagen;
        }

        return Storage::disk('public')->url($this->imagen);
    }
}
