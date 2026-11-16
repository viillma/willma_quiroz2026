<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CarouselSlide extends Model
{
    protected $fillable = [
        'titulo',
        'descripcion',
        'boton_texto',
        'boton_enlace',
        'imagen',
        'duracion',
        'orden',
        'activo',
    ];

    protected $appends = ['imagen_url'];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'duracion' => 'integer',
            'orden' => 'integer',
        ];
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

    public function scopeActivos($query)
    {
        return $query->where('activo', true)->orderBy('orden');
    }
}
