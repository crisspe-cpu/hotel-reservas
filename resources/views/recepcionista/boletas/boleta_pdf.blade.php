<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #111; }

    .boleta-wrap { max-width: 600px; margin: 0 auto; padding: 30px; }

    /* ── Encabezado ── */
    .enc { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
    .hotel-name { font-size: 22px; font-weight: 800; color: #534AB7; letter-spacing: -0.5px; }
    .hotel-sub  { font-size: 11px; color: #6b7280; margin-top: 2px; }
    .boleta-id  { text-align: right; }
    .boleta-id .num { font-size: 20px; font-weight: 700; color: #534AB7; }
    .boleta-id .fecha { font-size: 11px; color: #6b7280; margin-top: 2px; }

    .divider { height: 1.5px; background: linear-gradient(90deg, #534AB7, #7F77DD, #EEEDFE); margin: 16px 0; }
    .thin-divider { height: 0.5px; background: #e5e7eb; margin: 12px 0; }

    /* ── Datos del cliente ── */
    .section-title {
        font-size: 9px; font-weight: 700; color: #6b7280;
        text-transform: uppercase; letter-spacing: .08em; margin-bottom: 8px;
    }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 16px; }
    .info-item .lbl { font-size: 9px; color: #9ca3af; text-transform: uppercase; letter-spacing: .06em; }
    .info-item .val { font-size: 12px; font-weight: 600; color: #111; margin-top: 1px; }

    /* ── Detalle de habitaciones ── */
    table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    thead th {
        font-size: 9px; font-weight: 700; color: #fff; background: #534AB7;
        padding: 6px 10px; text-align: left; text-transform: uppercase; letter-spacing: .06em;
    }
    thead th.r { text-align: right; }
    tbody td { padding: 8px 10px; border-bottom: 0.5px solid #e5e7eb; font-size: 11px; }
    tbody td.r { text-align: right; }
    tbody tr:last-child td { border-bottom: none; }

    /* ── Totales ── */
    .totales { background: #F5F4FE; border-radius: 8px; padding: 14px 16px; margin-bottom: 20px; }
    .tot-row { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 6px; }
    .tot-row.final { font-size: 15px; font-weight: 800; color: #534AB7; margin-top: 8px; padding-top: 8px; border-top: 1px solid #D0CBFF; margin-bottom: 0; }
    .tot-row .lbl { color: #6b7280; }

    /* ── Pagos ── */
    .pagos-section { margin-bottom: 20px; }
    .pago-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 6px 10px; border-radius: 6px; margin-bottom: 4px;
        background: #F9FAFB; border: 0.5px solid #e5e7eb;
    }
    .pago-row .metodo { font-size: 11px; font-weight: 600; }
    .pago-row .fecha  { font-size: 10px; color: #9ca3af; }
    .pago-row .monto  { font-size: 12px; font-weight: 700; color: #16a34a; }

    .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: 700; }
    .m-efectivo { background: #CCFBF1; color: #0f766e; }
    .m-tarjeta  { background: #DBEAFE; color: #1d4ed8; }
    .m-yape     { background: #EDE9FE; color: #7c3aed; }
    .m-plin     { background: #E0F2FE; color: #0369a1; }

    /* ── Footer ── */
    .bol-footer {
        text-align: center; color: #9ca3af; font-size: 10px; margin-top: 24px;
        padding-top: 16px; border-top: 0.5px solid #e5e7eb;
    }
    .bol-footer p { margin-bottom: 3px; }

    /* ── Sello ── */
    .sello {
        border: 2px solid #534AB7; color: #534AB7; font-size: 14px; font-weight: 800;
        padding: 6px 18px; display: inline-block; border-radius: 4px;
        text-transform: uppercase; letter-spacing: .1em; transform: rotate(-8deg);
        margin-bottom: 12px;
    }
</style>
</head>
<body>
<div class="boleta-wrap">

    {{-- Encabezado --}}
    <div class="enc">
        <div>
            <div class="hotel-name">HotelSys</div>
            <div class="hotel-sub">Sistema de Gestión Hotelera</div>
        </div>
        <div class="boleta-id">
            <div class="num">BOLETA N° {{ str_pad($boleta->id_boleta, 6, '0', STR_PAD_LEFT) }}</div>
            <div class="fecha">Emitida el {{ $boleta->fecha_emision->format('d/m/Y \a \l\a\s H:i') }}</div>
        </div>
    </div>

    <div class="divider"></div>

    {{-- Datos del cliente --}}
    <div class="section-title"><i>●</i> Datos del cliente</div>
    <div class="info-grid">
        <div class="info-item">
            <div class="lbl">Nombre completo</div>
            <div class="val">{{ $boleta->reserva->cliente->nombre }} {{ $boleta->reserva->cliente->apellido }}</div>
        </div>
        <div class="info-item">
            <div class="lbl">{{ strtoupper($boleta->reserva->cliente->tipo_documento) }}</div>
            <div class="val">{{ $boleta->reserva->cliente->documento }}</div>
        </div>
        <div class="info-item">
            <div class="lbl">País</div>
            <div class="val">{{ $boleta->reserva->cliente->pais ?? '—' }}</div>
        </div>
        <div class="info-item">
            <div class="lbl">Teléfono</div>
            <div class="val">{{ $boleta->reserva->cliente->telefono ?? '—' }}</div>
        </div>
    </div>

    <div class="thin-divider"></div>

    {{-- Datos de la reserva --}}
    <div class="section-title"><i>●</i> Reserva #{{ $boleta->reserva->id_reserva }}</div>
    <div class="info-grid" style="margin-bottom:12px">
        <div class="info-item">
            <div class="lbl">Check-in</div>
            <div class="val">{{ $boleta->reserva->fecha_entrada->format('d/m/Y') }}</div>
        </div>
        <div class="info-item">
            <div class="lbl">Check-out</div>
            <div class="val">{{ $boleta->reserva->fecha_salida->format('d/m/Y') }}</div>
        </div>
        <div class="info-item">
            <div class="lbl">N° noches</div>
            <div class="val">{{ $boleta->reserva->noches }}</div>
        </div>
        <div class="info-item">
            <div class="lbl">Huéspedes</div>
            <div class="val">{{ $boleta->reserva->num_huespedes }}</div>
        </div>
    </div>

    {{-- Habitaciones --}}
    <table>
        <thead>
            <tr>
                <th>Habitación</th>
                <th>Tipo</th>
                <th>Capacidad</th>
                <th>Noches</th>
                <th class="r">Precio/noche</th>
                <th class="r">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($boleta->reserva->habitaciones as $h)
            @php
                $noches    = $boleta->reserva->noches;
                $subtotal  = $h->tipo->precio_base * $noches;
            @endphp
            <tr>
                <td><strong>Hab. {{ $h->numero }}</strong> — Piso {{ $h->piso }}</td>
                <td>{{ $h->tipo->nombre }}</td>
                <td style="text-align:center">{{ $h->tipo->capacidad }} pers.</td>
                <td style="text-align:center">{{ $noches }}</td>
                <td class="r">S/ {{ number_format($h->tipo->precio_base, 2) }}</td>
                <td class="r"><strong>S/ {{ number_format($subtotal, 2) }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="thin-divider"></div>

    {{-- Detalle de pagos --}}
    <div class="pagos-section">
        <div class="section-title"><i>●</i> Pagos registrados</div>
        @foreach($boleta->reserva->pagos as $pago)
        <div class="pago-row">
            <div>
                <span class="badge m-{{ $pago->metodo_pago }}">{{ ucfirst($pago->metodo_pago) }}</span>
                <span class="fecha" style="margin-left:8px">{{ $pago->fecha_pago->format('d/m/Y H:i') }}</span>
            </div>
            <div class="monto">S/ {{ number_format($pago->monto, 2) }}</div>
        </div>
        @endforeach
    </div>

    {{-- Totales --}}
    <div class="totales">
        <div class="tot-row">
            <span class="lbl">Precio total reserva</span>
            <span>S/ {{ number_format($boleta->reserva->precio_total, 2) }}</span>
        </div>
        <div class="tot-row">
            <span class="lbl">Total pagado</span>
            <span style="color:#16a34a">S/ {{ number_format($boleta->reserva->pagos->sum('monto'), 2) }}</span>
        </div>
        <div class="tot-row">
            <span class="lbl">Boletas anteriores</span>
            <span>S/ {{ number_format($boleta->total_acumulado - $boleta->total, 2) }}</span>
        </div>
        <div class="tot-row final">
            <span>TOTAL ESTA BOLETA</span>
            <span>S/ {{ number_format($boleta->total, 2) }}</span>
        </div>
    </div>

    {{-- Atendido por --}}
    <div style="text-align:right;font-size:11px;color:#6b7280;margin-bottom:16px">
        Atendido por: <strong>{{ $boleta->usuario->name }}</strong>
    </div>

    {{-- Footer --}}
    <div class="bol-footer">
        <div style="margin-bottom:12px">
            <div class="sello">Emitida</div>
        </div>
        <p><strong>Gracias por su preferencia</strong></p>
        <p>Este documento es su comprobante de pago. Consérvelo.</p>
        <p style="margin-top:8px">Emitido el {{ $boleta->fecha_emision->format('d/m/Y \a \l\a\s H:i') }}</p>
    </div>

</div>
</body>
</html>