<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Portafolio extends Model
{
    protected $table = 'portafolios';

    protected $fillable = [
        'titulo',
        'client_id',
        'servicio_id',
        'categoria',
        'descripcion',
        'imagen_principal',
        'fecha',
        'publicar',
    ];

    protected $appends = ['imagen_url'];

    protected function casts(): array
    {
        return ['publicar' => 'boolean', 'fecha' => 'date'];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PortafolioImage::class)->orderBy('orden');
    }

    public function getImagenUrlAttribute(): ?string
    {
        if (! $this->imagen_principal) {
            return null;
        }
        if (filter_var($this->imagen_principal, FILTER_VALIDATE_URL)) {
            return $this->imagen_principal;
        }

        return Storage::disk('public')->url($this->imagen_principal);
    }

    public static function CATEGORIAS(): array
    {
        return [
            'Logos',
            'Flyers',
            'Fotografía',
            'Eventos',
            'Filmación',
            'Publicidad',
            'Redes sociales',
            'Impresión',
            'Otros',
        ];
    }
}
