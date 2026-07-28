<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Servicio extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'tiempo_estimado',
        'imagen',
        'estado',
    ];

    protected $appends = ['imagen_url'];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
        ];
    }

    public function getImagenUrlAttribute(): ?string
    {
        return $this->imagen ? Storage::disk('public')->url($this->imagen) : null;
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_service')
            ->withPivot(['quantity', 'unit_price', 'subtotal'])
            ->withTimestamps();
    }
}
