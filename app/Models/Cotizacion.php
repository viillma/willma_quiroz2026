<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cotizacion extends Model
{
    protected $table = 'cotizaciones';

    protected $fillable = [
        'numero',
        'client_id',
        'order_id',
        'fecha',
        'fecha_validez',
        'descuento',
        'total',
        'observaciones',
        'condiciones',
        'estado',
    ];

    public const STATUSES = [
        'borrador' => 'Borrador',
        'enviada' => 'Enviada',
        'aceptada' => 'Aceptada',
        'rechazada' => 'Rechazada',
        'vencida' => 'Vencida',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'fecha_validez' => 'date',
            'descuento' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function servicios(): BelongsToMany
    {
        return $this->belongsToMany(Servicio::class, 'cotizacion_service')
            ->withPivot(['quantity', 'unit_price', 'subtotal'])->withTimestamps();
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->servicios->sum(fn ($s) => (float) $s->pivot->subtotal);
    }

    public function isExpired(): bool
    {
        return $this->fecha_validez !== null
            && $this->fecha_validez->isPast()
            && in_array($this->estado, ['borrador', 'enviada']);
    }

    public function recalculateTotals(): void
    {
        $subtotal = $this->subtotal;
        $descuento = (float) ($this->descuento ?? 0);
        $this->update(['total' => max(0, $subtotal - $descuento)]);
    }

    public static function generateNumber(): string
    {
        $year = now()->year;
        $last = self::whereYear('created_at', $year)->latest()->first();
        $next = $last ? intval(substr((string) $last->numero, -4)) + 1 : 1;

        return 'COT-'.$year.'-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
