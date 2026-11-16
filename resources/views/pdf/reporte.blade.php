@php
    use App\Models\BusinessSetting;
    use Illuminate\Support\Facades\Storage;

    $businessName = BusinessSetting::get('business_name') ?: 'CREAPIX';
    $businessLogo = BusinessSetting::get('business_logo');
    $logoPath = $businessLogo ? public_path('storage/'.$businessLogo) : null;
    $logoSrc = ($logoPath && file_exists($logoPath)) ? $logoPath : null;
    $address = collect([
        BusinessSetting::get('business_address'),
        BusinessSetting::get('business_city'),
        BusinessSetting::get('business_country'),
    ])->filter()->implode(', ');
    $labels = [
        'ingresos' => 'Ingresos',
        'pedidos' => 'Pedidos',
        'servicios' => 'Servicios más solicitados',
        'clientes' => 'Clientes frecuentes',
        'pagos' => 'Pagos',
        'saldos' => 'Saldos por cobrar',
        'cotizaciones' => 'Cotizaciones',
    ];
    $title = $labels[$type] ?? ucfirst($type);
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte {{ $title }}</title>
    <style>
        * { font-family: "DejaVu Sans", sans-serif; }
        body { margin: 0; color: #171A2B; font-size: 11px; }
        .header { padding: 0 0 16px; border-bottom: 3px solid #F43F7A; }
        .header-table { width: 100%; }
        .brand-name { font-size: 22px; font-weight: bold; color: #171A2B; }
        .brand-meta { font-size: 10px; color: #6b6f80; margin-top: 2px; }
        .logo { height: 52px; }
        .title { margin-top: 18px; font-size: 18px; font-weight: bold; color: #171A2B; }
        .period { font-size: 11px; color: #6b6f80; margin-top: 2px; }
        .meta { margin: 14px 0; font-size: 10px; color: #6b6f80; text-align: right; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.data thead th {
            background: #171A2B;
            color: #ffffff;
            text-align: left;
            padding: 8px 10px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        table.data tbody td {
            padding: 7px 10px;
            border-bottom: 1px solid #ececf2;
            font-size: 10.5px;
        }
        table.data tbody tr:nth-child(even) td { background: #faf9fc; }
        .empty { padding: 24px; text-align: center; color: #9aa0b0; }
        .footer { margin-top: 22px; font-size: 9px; color: #9aa0b0; text-align: center; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; background: #F43F7A; color: #fff; font-size: 9px; }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    @if ($logoSrc)
                        <img src="{{ $logoSrc }}" class="logo" alt="{{ $businessName }}">
                    @endif
                </td>
                <td style="text-align: right;">
                    <div class="brand-name">{{ $businessName }}</div>
                    @if ($address)
                        <div class="brand-meta">{{ $address }}</div>
                    @endif
                    @if (BusinessSetting::get('business_phone') || BusinessSetting::get('business_email'))
                        <div class="brand-meta">
                            {{ BusinessSetting::get('business_phone') }}
                            {{ BusinessSetting::get('business_phone') && BusinessSetting::get('business_email') ? ' · ' : '' }}
                            {{ BusinessSetting::get('business_email') }}
                        </div>
                    @endif
                </td>
            </tr>
        </table>

        <div class="title">Reporte de {{ $title }}</div>
        <div class="period">Período: {{ $start }} — {{ $end }}</div>
    </div>

    <div class="meta">
        <span class="badge">CREAPIX</span>
        Generado el {{ now()->format('d/m/Y H:i') }} · {{ count($rows) }} registro(s)
    </div>

    @if (count($rows) === 0)
        <div class="empty">No hay registros para el período seleccionado.</div>
    @else
        <table class="data">
            <thead>
                <tr>
                    @foreach ($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        @foreach ($row as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Documento generado automáticamente por el sistema de gestión {{ $businessName }}.
    </div>
</body>
</html>