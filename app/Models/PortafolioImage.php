<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PortafolioImage extends Model
{
    protected $table = 'portafolio_images';

    protected $fillable = ['portafolio_id', 'imagen_path', 'orden'];

    protected $appends = ['url'];

    public function portafolio(): BelongsTo
    {
        return $this->belongsTo(Portafolio::class);
    }

    public function getUrlAttribute(): string
    {
        if (filter_var($this->imagen_path, FILTER_VALIDATE_URL)) {
            return $this->imagen_path;
        }

        return Storage::disk('public')->url($this->imagen_path);
    }
}
