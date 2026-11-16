<?php

namespace App\Support;

use App\Models\Cotizacion;
use App\Models\Order;

class InvoiceCalculator
{
    /**
     * Calcula el subtotal a partir de los servicios del pedido/cotización.
     */
    public static function subtotal(Order|Cotizacion $model): float
    {
        $items = $model instanceof Order ? $model->servicios : $model->servicios;

        return (float) $items->sum(fn ($s) => (float) $s->pivot->subtotal);
    }

    /**
     * total = subtotal - descuento
     */
    public static function total(float $subtotal, float $descuento): float
    {
        return max(0, $subtotal - $descuento);
    }

    /**
     * saldo = total - pagos confirmados
     */
    public static function balance(Order $order): float
    {
        $paid = (float) $order->payments()->where('status', 'confirmado')->sum('amount');

        return max(0, (float) $order->total - $paid);
    }
}
