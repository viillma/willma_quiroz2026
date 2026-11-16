<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'code',
        'name',
        'tipo_cliente',
        'ci_nit',
        'phone',
        'whatsapp',
        'email',
        'direccion',
        'company',
        'notes',
        'activo',
    ];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function cotizaciones(): HasMany
    {
        return $this->hasMany(Cotizacion::class);
    }

    public function payments(): HasMany
    {
        return $this->hasManyThrough(Payment::class, Order::class);
    }

    public function getTotalCompradoAttribute(): float
    {
        return (float) $this->orders()->sum('total');
    }

    public function getTotalPagadoAttribute(): float
    {
        return (float) $this->orders()
            ->join('payments', 'orders.id', '=', 'payments.order_id')
            ->where('payments.status', 'confirmado')
            ->sum('payments.amount');
    }

    public function getSaldoPendienteAttribute(): float
    {
        return max(0, $this->total_comprado - $this->total_pagado);
    }

    public static function generateCode(): string
    {
        $year = now()->year;
        $last = self::whereYear('created_at', $year)->latest()->first();
        $next = $last ? intval(substr((string) $last->code, -4)) + 1 : 1;

        return 'CLI-'.$year.'-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
