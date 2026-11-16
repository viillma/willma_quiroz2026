<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'code',
        'client_id',
        'designer_id',
        'status',
        'notes',
        'total',
        'descuento',
        'adelanto',
        'forma_pago',
        'responsable',
        'delivery_date',
        'delivered_at',
    ];

    public const STATUSES = [
        'nuevo' => 'Nuevo',
        'confirmado' => 'Confirmado',
        'en_proceso' => 'En proceso',
        'en_revision' => 'En revisión',
        'terminado' => 'Terminado',
        'entregado' => 'Entregado',
        'cancelado' => 'Cancelado',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'descuento' => 'decimal:2',
            'adelanto' => 'decimal:2',
            'delivery_date' => 'date',
            'delivered_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function designer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'designer_id');
    }

    public function cotizacion(): BelongsTo
    {
        return $this->belongsTo(Cotizacion::class);
    }

    public function servicios(): BelongsToMany
    {
        return $this->belongsToMany(Servicio::class, 'order_service')
            ->withPivot(['quantity', 'unit_price', 'subtotal'])->withTimestamps();
    }

    public function designFiles(): HasMany
    {
        return $this->hasMany(DesignFile::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(OrderHistory::class)->latest();
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->servicios->sum(fn ($s) => (float) $s->pivot->subtotal);
    }

    public function getTotalPagadoAttribute(): float
    {
        return (float) $this->payments()->where('status', 'confirmado')->sum('amount');
    }

    public function getBalanceAttribute(): float
    {
        return max(0, (float) $this->total - $this->total_pagado);
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->balance <= 0;
    }

    public function getPaymentStateAttribute(): string
    {
        if ($this->total_pagado <= 0) {
            return 'pendiente';
        }
        if ($this->is_paid) {
            return 'pagado';
        }

        return 'parcial';
    }

    public function getPaidRatioAttribute(): float
    {
        if ((float) $this->total <= 0) {
            return 0;
        }

        return round(($this->total_pagado / (float) $this->total), 2);
    }

    public function isOverdue(): bool
    {
        return $this->delivery_date !== null
            && $this->delivery_date->isPast()
            && ! in_array($this->status, ['entregado', 'cancelado']);
    }

    public function recalculateTotals(): void
    {
        $subtotal = $this->subtotal;
        $descuento = (float) ($this->descuento ?? 0);
        $this->update(['total' => max(0, $subtotal - $descuento)]);
    }

    public static function generateCode(): string
    {
        $year = now()->year;
        $last = self::whereYear('created_at', $year)->latest()->first();
        $next = $last ? intval(substr((string) $last->code, -4)) + 1 : 1;

        return 'PED-'.$year.'-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
