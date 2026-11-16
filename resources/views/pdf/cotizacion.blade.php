@php
    $sym = $business['currency_symbol'];
    $fmt = fn ($n) => $sym.' '.number_format((float) $n, 2);
    $subtotal = $cotizacion->servicios->sum(fn ($s) => (float) $s->pivot->subtotal);
    $descuento = (float) ($cotizacion->descuento ?? 0);
    $estadoLabel = \App\Models\Cotizacion::STATUSES[$cotizacion->estado] ?? $cotizacion->estado;
    $address = collect([$business['address'], $business['city'], $business['country']])->filter()->implode(', ');
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Cotización {{ $cotizacion->numero }}</title>
    <style>
        * { font-family: "DejaVu Sans", sans-serif; }
        body { margin: 0; color: #171A2B; font-size: 11px; }
        .accent { height: 6px; background: #F43F7A; }
        .header { padding: 22px 0 18px; border-bottom: 1px solid #ececf2; }
        .header-table { width: 100%; }
        .brand-name { font-size: 24px; font-weight: bold; }
        .brand-meta { font-size: 10px; color: #6b6f80; margin-top: 3px; }
        .logo { height: 58px; }
        .doc-title { text-align: right; }
        .doc-title h1 { margin: 0; font-size: 20px; color: #F43F7A; letter-spacing: 0.05em; }
        .doc-number { font-size: 14px; font-weight: bold; margin-top: 4px; }
        .doc-state { display: inline-block; margin-top: 6px; padding: 3px 12px; border-radius: 999px; background: #DCCBFF; color: #171A2B; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.08em; }
        .info { width: 100%; margin-top: 20px; }
        .info td { vertical-align: top; }
        .info-box { background: #faf9fc; border: 1px solid #ececf2; border-radius: 10px; padding: 12px 14px; }
        .info-label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.08em; color: #9aa0b0; margin-bottom: 4px; }
        .info-value { font-size: 11px; font-weight: bold; }
        .info-line { font-size: 10px; color: #6b6f80; margin-top: 2px; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 22px; }
        table.items thead th { background: #171A2B; color: #fff; text-align: left; padding: 9px 11px; font-size: 9.5px; text-transform: uppercase; letter-spacing: 0.06em; }
        table.items tbody td { padding: 9px 11px; border-bottom: 1px solid #ececf2; font-size: 10.5px; }
        table.items tbody tr:nth-child(even) td { background: #faf9fc; }
        .right { text-align: right; }
        .center { text-align: center; }
        .totals { width: 46%; margin-left: 54%; margin-top: 14px; }
        .totals td { padding: 6px 0; font-size: 11px; }
        .totals .label { color: #6b6f80; }
        .totals .value { text-align: right; font-weight: bold; }
        .totals .grand td { border-top: 2px solid #171A2B; padding-top: 10px; font-size: 15px; color: #F43F7A; }
        .notes { margin-top: 26px; }
        .notes h3 { font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em; color: #9aa0b0; margin: 0 0 5px; }
        .notes p { margin: 0 0 12px; font-size: 10.5px; color: #4a4e60; line-height: 1.5; }
        .footer { margin-top: 30px; padding-top: 12px; border-top: 1px solid #ececf2; text-align: center; font-size: 9px; color: #9aa0b0; }
    </style>
</head>
<body>
    <div class="accent"></div>

    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    @if ($business['logo'])
                        <img src="{{ $business['logo'] }}" class="logo" alt="{{ $business['name'] }}">
                    @else
                        <div class="brand-name">{{ $business['name'] }}</div>
                    @endif
                    <div class="brand-meta">
                        @if ($business['nit']) NIT: {{ $business['nit'] }}<br> @endif
                        @if ($address) {{ $address }}<br> @endif
                        @if ($business['phone']) Tel: {{ $business['phone'] }} @endif
                        @if ($business['email']) · {{ $business['email'] }} @endif
                    </div>
                </td>
                <td class="doc-title">
                    <h1>COTIZACIÓN</h1>
                    <div class="doc-number">{{ $cotizacion->numero }}</div>
                    <div class="doc-state">{{ $estadoLabel }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="info">
        <tr>
            <td style="width: 55%; padding-right: 14px;">
                <div class="info-box">
                    <div class="info-label">Cliente</div>
                    <div class="info-value">{{ $cotizacion->client?->name ?? 'Sin cliente' }}</div>
                    @if ($cotizacion->client?->code)
                        <div class="info-line">Código: {{ $cotizacion->client->code }}</div>
                    @endif
                    @if ($cotizacion->client?->company)
                        <div class="info-line">{{ $cotizacion->client->company }}</div>
                    @endif
                    @if ($cotizacion->client?->phone)
                        <div class="info-line">Tel: {{ $cotizacion->client->phone }}</div>
                    @endif
                    @if ($cotizacion->client?->email)
                        <div class="info-line">{{ $cotizacion->client->email }}</div>
                    @endif
                </div>
            </td>
            <td style="width: 45%;">
                <div class="info-box">
                    <div class="info-label">Detalles</div>
                    <div class="info-line"><strong>Fecha:</strong> {{ $cotizacion->fecha?->format('d/m/Y') }}</div>
                    <div class="info-line"><strong>Válida hasta:</strong> {{ $cotizacion->fecha_validez?->format('d/m/Y') ?? 'Sin fecha límite' }}</div>
                    <div class="info-line"><strong>Emitida:</strong> {{ now()->format('d/m/Y H:i') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 8%;">#</th>
                <th>Descripción</th>
                <th class="center" style="width: 12%;">Cant.</th>
                <th class="right" style="width: 18%;">P. Unitario</th>
                <th class="right" style="width: 18%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cotizacion->servicios as $index => $servicio)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $servicio->nombre }}</strong>
                        @if ($servicio->codigo)
                            <div style="font-size: 9px; color: #9aa0b0;">Cód. {{ $servicio->codigo }}</div>
                        @endif
                    </td>
                    <td class="center">{{ (int) $servicio->pivot->quantity }}</td>
                    <td class="right">{{ $fmt($servicio->pivot->unit_price) }}</td>
                    <td class="right">{{ $fmt($servicio->pivot->subtotal) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="label">Subtotal</td>
            <td class="value">{{ $fmt($subtotal) }}</td>
        </tr>
        @if ($descuento > 0)
            <tr>
                <td class="label">Descuento</td>
                <td class="value">- {{ $fmt($descuento) }}</td>
            </tr>
        @endif
        <tr class="grand">
            <td class="label" style="color:#171A2B; font-weight:bold;">TOTAL</td>
            <td class="value">{{ $fmt($cotizacion->total) }}</td>
        </tr>
    </table>

    <div class="notes">
        @if ($cotizacion->observaciones)
            <h3>Observaciones</h3>
            <p>{{ $cotizacion->observaciones }}</p>
        @endif
        @if ($cotizacion->condiciones)
            <h3>Condiciones</h3>
            <p>{{ $cotizacion->condiciones }}</p>
        @endif
    </div>

    <div class="footer">
        {{ $business['name'] }} · Documento generado automáticamente · Esta cotización tiene una validez sujeta a los términos indicados.
    </div>
</body>
</html>