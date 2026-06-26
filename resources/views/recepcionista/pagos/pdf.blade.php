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
        display: flex; justify-content: space-between; align-items: center;
    }
    .header h1 { font-size: 18px; font-weight: 700; }
    .header .sub { font-size: 11px; opacity: .8; margin-top: 2px; }
    .header .logo { font-size: 28px; }

    .meta-row {
        display: flex; gap: 16px; margin-bottom: 14px;
        background: #F5F4FE; border-radius: 8px; padding: 10px 14px;
    }
    .meta-item { flex: 1; }
    .meta-label { font-size: 9px; font-weight: 700; color: #6b7280;
                  text-transform: uppercase; letter-spacing: .06em; margin-bottom: 2px; }
    .meta-val { font-size: 13px; font-weight: 600; color: #111; }

    table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    thead th {
        background: #534AB7; color: #fff; font-size: 9px; font-weight: 600;
        padding: 7px 10px; text-align: left; text-transform: uppercase; letter-spacing: .06em;
    }
    thead th.r { text-align: right; }
    tbody tr:nth-child(even) td { background: #F8F8FF; }
    tbody td { padding: 7px 10px; border-bottom: 0.5px solid #e5e7eb; font-size: 10px; }
    tbody td.r { text-align: right; font-weight: 600; }
    tbody tr:last-child td { border-bottom: none; }

    .total-row td { background: #EEF2FF !important; font-weight: 700; font-size: 11px; border-top: 1.5px solid #534AB7; }

    .badge {
        display: inline-block; padding: 2px 7px; border-radius: 10px;
        font-size: 9px; font-weight: 700;
    }
    .m-efectivo { background: #CCFBF1; color: #0f766e; }
    .m-tarjeta  { background: #DBEAFE; color: #1d4ed8; }
    .m-yape     { background: #EDE9FE; color: #7c3aed; }
    .m-plin     { background: #E0F2FE; color: #0369a1; }

    .footer { border-top: 0.5px solid #e5e7eb; padding-top: 10px; color: #9ca3af; font-size: 9px;
              display: flex; justify-content: space-between; }
</style>
</head>
<body>

<div class="header">
    <div>
        <h1>Reporte de Pagos</h1>
        <div class="sub">
            @if($desde && $hasta)
                Período: {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}
            @else
                Todos los registros
            @endif
        </div>
    </div>
    <div>
        <div style="font-size:11px; text-align:right; opacity:.8">Generado: {{ now()->format('d/m/Y H:i') }}</div>
        <div style="font-size:11px; text-align:right; opacity:.8">Total: {{ $pagos->count() }} pagos</div>
    </div>
</div>

<div class="meta-row">
    <div class="meta-item">
        <div class="meta-label">Total registros</div>
        <div class="meta-val">{{ $pagos->count() }}</div>
    </div>
    <div class="meta-item">
        <div class="meta-label">Monto total</div>
        <div class="meta-val" style="color:#534AB7">S/ {{ number_format($totalGeneral, 2) }}</div>
    </div>
    @foreach($pagos->groupBy('metodo_pago') as $metodo => $grupo)
    <div class="meta-item">
        <div class="meta-label">{{ ucfirst($metodo) }}</div>
        <div class="meta-val">S/ {{ number_format($grupo->sum('monto'), 2) }}</div>
    </div>
    @endforeach
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Reserva</th>
            <th>Cliente</th>
            <th>Documento</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Método</th>
            <th class="r">Monto</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pagos as $p)
        <tr>
            <td>{{ $p->id_pago }}</td>
            <td>#{{ $p->id_reserva }}</td>
            <td>{{ $p->reserva->cliente->nombre }} {{ $p->reserva->cliente->apellido }}</td>
            <td>{{ $p->reserva->cliente->documento }}</td>
            <td>{{ $p->fecha_pago->format('d/m/Y') }}</td>
            <td>{{ $p->fecha_pago->format('H:i') }}</td>
            <td><span class="badge m-{{ $p->metodo_pago }}">{{ ucfirst($p->metodo_pago) }}</span></td>
            <td class="r">S/ {{ number_format($p->monto, 2) }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="7" style="text-align:right; padding-right:14px">TOTAL GENERAL</td>
            <td class="r">S/ {{ number_format($totalGeneral, 2) }}</td>
        </tr>
    </tbody>
</table>

<div class="footer">
    <span>Sistema de Gestión Hotelera</span>
    <span>Reporte generado el {{ now()->format('d/m/Y \a \l\a\s H:i') }}</span>
</div>

</body>
</html>