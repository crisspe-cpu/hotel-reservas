<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #111; }

    .header {
        background: #534AB7; color: #fff;
        padding: 16px 20px; margin-bottom: 16px;
    }
    .header h1 { font-size: 18px; font-weight: 700; }
    .header .sub { font-size: 11px; opacity: .8; margin-top: 2px; }

    .meta-row {
        display: flex; gap: 16px; margin-bottom: 14px;
        background: #F5F4FE; border-radius: 8px; padding: 10px 14px;
    }
    .meta-item .label { font-size: 9px; font-weight: 700; color: #6b7280;
                        text-transform: uppercase; letter-spacing: .06em; margin-bottom: 2px; }
    .meta-item .val   { font-size: 13px; font-weight: 700; color: #111; }

    table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    thead th {
        background: #534AB7; color: #fff; font-size: 9px; font-weight: 600;
        padding: 7px 10px; text-align: left; text-transform: uppercase; letter-spacing: .06em;
    }
    thead th.r { text-align: right; }
    tbody tr:nth-child(even) td { background: #F8F8FF; }
    tbody td { padding: 7px 10px; border-bottom: 0.5px solid #e5e7eb; font-size: 10px; vertical-align: middle; }
    tbody td.r { text-align: right; font-weight: 600; }
    .total-row td { background: #EEF2FF !important; font-weight: 700; font-size: 11px; border-top: 1.5px solid #534AB7; }

    .footer { border-top: 0.5px solid #e5e7eb; padding-top: 10px; color: #9ca3af; font-size: 9px;
              display: flex; justify-content: space-between; margin-top: 10px; }
</style>
</head>
<body>

<div class="header">
    <h1>Reporte de Boletas Emitidas</h1>
    <div class="sub">
        @if($desde && $hasta)
            Período: {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}
        @else
            Todos los registros · Generado: {{ now()->format('d/m/Y H:i') }}
        @endif
    </div>
</div>

<div class="meta-row">
    <div class="meta-item">
        <div class="label">Total boletas</div>
        <div class="val">{{ $boletas->count() }}</div>
    </div>
    <div class="meta-item">
        <div class="label">Monto total facturado</div>
        <div class="val" style="color:#534AB7">S/ {{ number_format($totalGeneral, 2) }}</div>
    </div>
    <div class="meta-item">
        <div class="label">Generado</div>
        <div class="val" style="font-size:11px">{{ now()->format('d/m/Y H:i') }}</div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Reserva</th>
            <th>Cliente</th>
            <th>Documento</th>
            <th>Emitido por</th>
            <th>Fecha emisión</th>
            <th class="r">Total</th>
            <th class="r">Acumulado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($boletas as $b)
        <tr>
            <td>{{ $b->id_boleta }}</td>
            <td>#{{ $b->id_reserva }}</td>
            <td>{{ $b->reserva->cliente->nombre }} {{ $b->reserva->cliente->apellido }}</td>
            <td>{{ $b->reserva->cliente->documento }}</td>
            <td>{{ $b->usuario->name }}</td>
            <td>{{ $b->fecha_emision->format('d/m/Y H:i') }}</td>
            <td class="r">S/ {{ number_format($b->total, 2) }}</td>
            <td class="r">S/ {{ number_format($b->total_acumulado, 2) }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="6" style="text-align:right;padding-right:14px">TOTAL FACTURADO</td>
            <td class="r">S/ {{ number_format($totalGeneral, 2) }}</td>
            <td></td>
        </tr>
    </tbody>
</table>

<div class="footer">
    <span>Sistema de Gestión Hotelera</span>
    <span>Reporte generado el {{ now()->format('d/m/Y \a \l\a\s H:i') }}</span>
</div>

</body>
</html>