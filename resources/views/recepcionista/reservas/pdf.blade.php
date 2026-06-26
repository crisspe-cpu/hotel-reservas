<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #111; }

    .header { background: #534AB7; color: #fff; padding: 16px 20px; margin-bottom: 16px; }
    .header h1 { font-size: 18px; font-weight: 700; }
    .header .sub { font-size: 11px; opacity: .8; margin-top: 2px; }

    .meta-row {
        display: flex; gap: 14px; margin-bottom: 14px;
        background: #F5F4FE; border-radius: 8px; padding: 10px 14px;
    }
    .mi .lbl { font-size: 9px; font-weight: 700; color: #6b7280; text-transform: uppercase;
               letter-spacing: .06em; margin-bottom: 2px; }
    .mi .val  { font-size: 12px; font-weight: 700; }

    table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    thead th {
        background: #534AB7; color: #fff; font-size: 9px; font-weight: 600;
        padding: 6px 8px; text-align: left; text-transform: uppercase; letter-spacing: .05em;
    }
    thead th.r { text-align: right; }
    tbody tr:nth-child(even) td { background: #F8F8FF; }
    tbody td { padding: 6px 8px; border-bottom: 0.5px solid #e5e7eb; font-size: 10px; }
    tbody td.r { text-align: right; font-weight: 600; }

    .total-row td { background: #EEF2FF !important; font-weight: 700; font-size: 11px; border-top: 1.5px solid #534AB7; }

    .badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 9px; font-weight: 700; }
    .s-confirmada { background: #DCFCE7; color: #166534; }
    .s-pendiente  { background: #FEF3C7; color: #92400e; }
    .s-cancelada  { background: #FEE2E2; color: #991b1b; }
    .s-finalizada { background: #E0F2FE; color: #075985; }

    .footer { border-top: 0.5px solid #e5e7eb; padding-top: 10px; color: #9ca3af; font-size: 9px;
              display: flex; justify-content: space-between; margin-top: 10px; }
</style>
</head>
<body>

<div class="header">
    <h1>Reporte de Reservas</h1>
    <div class="sub">
        @if($desde && $hasta)
            Check-in entre {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }} y {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}
            @if($estado) · Estado: {{ ucfirst($estado) }} @endif
        @elseif($estado)
            Estado: {{ ucfirst($estado) }} · Todos los períodos
        @else
            Todos los registros
        @endif
        — Generado: {{ now()->format('d/m/Y H:i') }}
    </div>
</div>

<div class="meta-row">
    <div class="mi">
        <div class="lbl">Total reservas</div>
        <div class="val">{{ $reservas->count() }}</div>
    </div>
    <div class="mi">
        <div class="lbl">Monto total</div>
        <div class="val" style="color:#534AB7">S/ {{ number_format($totalGeneral, 2) }}</div>
    </div>
    @foreach($reservas->groupBy('estado') as $est => $grupo)
    <div class="mi">
        <div class="lbl">{{ ucfirst($est) }}</div>
        <div class="val">{{ $grupo->count() }} · S/ {{ number_format($grupo->sum('precio_total'), 2) }}</div>
    </div>
    @endforeach
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Cliente</th>
            <th>Documento</th>
            <th>Habitación(es)</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Noches</th>
            <th>Estado</th>
            <th class="r">Total</th>
            <th class="r">Pagado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reservas as $r)
        @php $pagado = $r->pagos->sum('monto'); @endphp
        <tr>
            <td>{{ $r->id_reserva }}</td>
            <td>{{ $r->cliente->nombre }} {{ $r->cliente->apellido }}</td>
            <td>{{ $r->cliente->documento }}</td>
            <td>{{ $r->habitaciones->pluck('numero')->join(', ') }}</td>
            <td>{{ $r->fecha_entrada->format('d/m/Y') }}</td>
            <td>{{ $r->fecha_salida->format('d/m/Y') }}</td>
            <td style="text-align:center">{{ $r->fecha_entrada->diffInDays($r->fecha_salida) }}</td>
            <td><span class="badge s-{{ $r->estado }}">{{ ucfirst($r->estado) }}</span></td>
            <td class="r">S/ {{ number_format($r->precio_total, 2) }}</td>
            <td class="r" style="color:{{ $pagado >= $r->precio_total ? '#166534' : '#92400e' }}">
                S/ {{ number_format($pagado, 2) }}
            </td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="8" style="text-align:right;padding-right:10px">TOTAL GENERAL</td>
            <td class="r">S/ {{ number_format($totalGeneral, 2) }}</td>
            <td class="r">S/ {{ number_format($reservas->sum(fn($r) => $r->pagos->sum('monto')), 2) }}</td>
        </tr>
    </tbody>
</table>

<div class="footer">
    <span>Sistema de Gestión Hotelera</span>
    <span>Generado el {{ now()->format('d/m/Y \a \l\a\s H:i') }}</span>
</div>

</body>
</html>