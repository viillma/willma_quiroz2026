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
        'delivery_date',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
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

    public function servicios(): BelongsToMany
    {
        return $this->belongsToMany(Servicio::class, 'order_service')
            ->withPivot(['quantity', 'unit_price', 'subtotal'])
            ->withTimestamps();
    }

    public function designFiles(): HasMany
    {
        return $this->hasMany(DesignFile::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getBalanceAttribute(): float
    {
        return $this->total - $this->payments()->where('status', 'confirmado')->sum('amount');
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->balance <= 0;
    }

    public static function generateCode(): string
    {
        $year = now()->year;
        $last = self::whereYear('created_at', $year)->latest()->first();
        $next = $last ? intval(substr($last->code, -4)) + 1 : 1;

        return 'PED-'.$year.'-'.str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
