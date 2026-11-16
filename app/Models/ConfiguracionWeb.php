<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ConfiguracionWeb extends Model
{
    protected $table = 'configuraciones_web';

    protected $fillable = [
        'logo',
        'nombre_empresa',
    ];

    protected $appends = ['logo_url'];

    public static function primera(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            ['logo' => null, 'nombre_empresa' => null]
        );
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo) {
            return null;
        }

        if (filter_var($this->logo, FILTER_VALIDATE_URL)) {
            return $this->logo;
        }

        return Storage::disk('public')->url($this->logo);
    }
}
