<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Servicio extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'slug',
        'categoria',
        'descripcion',
        'precio',
        'costo_estimado',
        'unidad',
        'tiempo_estimado',
        'imagen',
        'estado',
        'publicado',
        'destacado',
        'orden',
        'seo_title',
        'seo_description',
        'caracteristicas',
        'incluye',
    ];

    protected $appends = ['imagen_url'];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'costo_estimado' => 'decimal:2',
            'publicado' => 'boolean',
            'destacado' => 'boolean',
            'caracteristicas' => 'array',
            'incluye' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Servicio $servicio) {
            if (empty($servicio->slug) && $servicio->nombre) {
                $servicio->slug = static::uniqueSlug($servicio->nombre, $servicio->id);
            }
        });
    }

    public static function uniqueSlug(string $nombre, ?int $ignoreId = null): string
    {
        $base = str($nombre)->slug()->limit(90, '');
        $slug = $base->toString();
        $i = 2;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q, $id) => $q->where('id', '!=', $id))->exists()) {
            $slug = $base->toString().'-'.$i;
            $i++;
        }

        return $slug;
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

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_service')
            ->withPivot(['quantity', 'unit_price', 'subtotal'])->withTimestamps();
    }

    public function cotizaciones(): BelongsToMany
    {
        return $this->belongsToMany(Cotizacion::class, 'cotizacion_service')
            ->withPivot(['quantity', 'unit_price', 'subtotal'])->withTimestamps();
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopePublicados($query)
    {
        return $query->where('publicado', true);
    }

    public function scopeDestacados($query)
    {
        return $query->where('destacado', true);
    }

    public static function CATEGORIAS(): array
    {
        return [
            'Diseño gráfico',
            'Fotografía',
            'Filmación',
            'Producción audiovisual',
            'Publicidad',
            'Impresión',
            'Otros',
        ];
    }
}
