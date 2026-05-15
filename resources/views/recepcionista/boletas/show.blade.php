@extends('layouts.app')

@section('title', 'Boleta #'.$boleta->id_boleta)
@section('page-title', 'Boleta #'.$boleta->id_boleta)
@section('breadcrumb', 'Boletas / Detalle')

@section('topbar-actions')
    <button onclick="window.print()" class="btn btn-ghost"><i class="bi bi-printer"></i> Imprimir</button>
@endsection

@section('content')
<div style="max-width:600px; margin:0 auto">
<div class="card" id="boleta-print">
    <div style="padding:32px">
        {{-- Encabezado --}}
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:28px">
            <div>
                <div style="font-size:24px; margin-bottom:4px">🏨</div>
                <div style="font-size:18px; font-weight:700; letter-spacing:-.02em">HotelApp</div>
                <div style="font-size:12px; color:var(--muted)">Sistema de Gestión de Reservas</div>
            </div>
            <div style="text-align:right">
                <div style="font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:.06em">Boleta N°</div>
                <div style="font-size:28px; font-weight:700; color:var(--accent2); font-family:var(--mono)">{{ str_pad($boleta->id_boleta, 6, '0', STR_PAD_LEFT) }}</div>
                <div style="font-size:12px; color:var(--muted)">{{ $boleta->fecha_emision->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <div style="height:1px; background:var(--border); margin-bottom:24px"></div>

        {{-- Cliente --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px">
            <div>
                <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Cliente</div>
                <div style="font-weight:600">{{ $boleta->reserva->cliente->nombre }} {{ $boleta->reserva->cliente->apellido }}</div>
                <div style="font-size:13px;color:var(--muted)">{{ strtoupper($boleta->reserva->cliente->tipo_documento) }}: {{ $boleta->reserva->cliente->documento }}</div>
                @if($boleta->reserva->cliente->pais)
                    <div style="font-size:13px;color:var(--muted)">{{ $boleta->reserva->cliente->pais }}</div>
                @endif
            </div>
            <div>
                <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Estadía</div>
                <div style="font-size:13px">Entrada: <strong>{{ $boleta->reserva->fecha_entrada->format('d/m/Y') }}</strong></div>
                <div style="font-size:13px">Salida: <strong>{{ $boleta->reserva->fecha_salida->format('d/m/Y') }}</strong></div>
                <div style="font-size:13px">Noches: <strong>{{ $boleta->reserva->noches }}</strong></div>
                <div style="font-size:13px">Huéspedes: <strong>{{ $boleta->reserva->num_huespedes }}</strong></div>
            </div>
        </div>

        {{-- Detalle habitaciones --}}
        <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px">Detalle de Habitaciones</div>
        <table style="width:100%; font-size:13px; margin-bottom:20px">
            <thead>
                <tr>
                    <th style="text-align:left;padding:8px 0;border-bottom:1px solid var(--border);color:var(--muted);font-size:11px;letter-spacing:.05em">Habitación</th>
                    <th style="text-align:left;padding:8px 0;border-bottom:1px solid var(--border);color:var(--muted);font-size:11px">Tipo</th>
                    <th style="text-align:right;padding:8px 0;border-bottom:1px solid var(--border);color:var(--muted);font-size:11px">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($boleta->reserva->habitaciones as $h)
                <tr>
                    <td style="padding:10px 0;border-bottom:1px solid var(--border)">Hab. {{ $h->numero }} — Piso {{ $h->piso }}</td>
                    <td style="padding:10px 0;border-bottom:1px solid var(--border);color:var(--muted)">{{ $h->tipo->nombre }}</td>
                    <td style="padding:10px 0;border-bottom:1px solid var(--border);text-align:right;font-weight:500">S/ {{ number_format($h->pivot->precio_aplicado, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagos --}}
        <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px">Pagos Recibidos</div>
        @foreach($boleta->reserva->pagos as $p)
        <div style="display:flex;justify-content:space-between;font-size:13px;padding:6px 0">
            <span style="color:var(--muted)">{{ ucfirst($p->metodo_pago) }} — {{ $p->fecha_pago->format('d/m/Y H:i') }}</span>
            <span>S/ {{ number_format($p->monto, 2) }}</span>
        </div>
        @endforeach

        <div style="height:1px;background:var(--border);margin:16px 0"></div>

        {{-- Total --}}
        <div style="display:flex;justify-content:space-between;align-items:center">
            <div style="font-size:14px;font-weight:600">TOTAL</div>
            <div style="font-size:26px;font-weight:700;color:var(--success)">S/ {{ number_format($boleta->total, 2) }}</div>
        </div>

        <div style="height:1px;background:var(--border);margin:20px 0"></div>

        <div style="text-align:center;font-size:12px;color:var(--muted)">
            Emitido por: {{ $boleta->usuario->name }} {{ $boleta->usuario->apellido }}<br>
            Gracias por su preferencia 🙏
        </div>
    </div>
</div>
</div>

@push('styles')
<style>
@media print {
    .sidebar, .topbar { display:none !important; }
    .main { margin-left:0 !important; }
    .content { padding:0 !important; }
    #boleta-print { border:none !important; background:#fff !important; color:#000 !important; }
}
</style>
@endpush
@endsection