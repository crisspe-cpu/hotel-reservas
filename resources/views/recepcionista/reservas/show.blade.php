@extends('layouts.app')

@section('title', 'Reserva #'.$reserva->id_reserva)
@section('page-title', 'Reserva #'.$reserva->id_reserva)
@section('breadcrumb', 'Reservas / Detalle')

@section('topbar-actions')
    @if($reserva->estado !== 'cancelada')
        <a href="{{ route('recepcionista.pagos.create', ['id_reserva' => $reserva->id_reserva]) }}" class="btn btn-success"><i class="bi bi-credit-card"></i> Registrar Pago</a>
        @if($reserva->estado === 'confirmada' && !$reserva->boleta)
            <form method="POST" action="{{ route('recepcionista.boletas.store') }}" style="display:inline">
                @csrf
                <input type="hidden" name="id_reserva" value="{{ $reserva->id_reserva }}">
                <button type="submit" class="btn btn-primary"><i class="bi bi-receipt"></i> Emitir Boleta</button>
            </form>
        @endif
    @endif
    <a href="{{ route('recepcionista.reservas.edit', $reserva) }}" class="btn btn-ghost"><i class="bi bi-pencil"></i> Editar</a>
@endsection

@section('content')
<div style="display:grid; grid-template-columns:1fr 1fr; gap:16px">

    {{-- Info reserva --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-calendar-event" style="color:var(--accent2)"></i> Datos de la Reserva</span>
            @php $map = ['confirmada'=>'success','pendiente'=>'warning','cancelada'=>'danger']; @endphp
            <span class="badge badge-{{ $map[$reserva->estado] ?? 'muted' }}">{{ $reserva->estado }}</span>
        </div>
        <div class="card-body">
            <table style="width:100%;font-size:13px">
                @foreach([
                    ['Cliente', $reserva->cliente->nombre.' '.$reserva->cliente->apellido],
                    ['Documento', $reserva->cliente->documento],
                    ['Entrada', $reserva->fecha_entrada->format('d/m/Y')],
                    ['Salida', $reserva->fecha_salida->format('d/m/Y')],
                    ['Noches', $reserva->noches],
                    ['Huéspedes', $reserva->num_huespedes],
                    ['Atendido por', $reserva->usuario->nombre.' '.$reserva->usuario->apellido],
                ] as [$k,$v])
                <tr>
                    <td style="color:var(--muted);padding:9px 0;border-bottom:1px solid var(--border)">{{ $k }}</td>
                    <td style="text-align:right;padding:9px 0;border-bottom:1px solid var(--border);font-weight:500">{{ $v }}</td>
                </tr>
                @endforeach
                <tr>
                    <td style="color:var(--muted);padding:9px 0">Total</td>
                    <td style="text-align:right;font-size:18px;font-weight:600;color:var(--accent2)">S/ {{ number_format($reserva->precio_total, 2) }}</td>
                </tr>
            </table>
            @if($reserva->notas)
                <div class="divider"></div>
                <div style="font-size:12px;color:var(--muted)">NOTAS</div>
                <p style="font-size:13px;margin-top:6px">{{ $reserva->notas }}</p>
            @endif
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:16px">
        {{-- Habitaciones --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-door-open" style="color:var(--accent2)"></i> Habitaciones</span>
            </div>
            <div class="card-body">
                @foreach($reserva->habitaciones as $h)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border)">
                    <div>
                        <div style="font-weight:500">Hab. {{ $h->numero }} — Piso {{ $h->piso }}</div>
                        <div style="font-size:12px;color:var(--muted)">{{ $h->tipo->nombre }} · Cap. {{ $h->tipo->capacidad }}</div>
                    </div>
                    <div style="font-weight:500;color:var(--accent2)">S/ {{ number_format($h->pivot->precio_aplicado, 2) }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Pagos --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-credit-card" style="color:var(--accent2)"></i> Pagos</span>
                @php $totalPagado = $reserva->pagos->sum('monto'); @endphp
                <span style="font-size:12px;color:var(--muted)">S/ {{ number_format($totalPagado, 2) }} / S/ {{ number_format($reserva->precio_total, 2) }}</span>
            </div>
            <div class="card-body">
                {{-- Barra de progreso --}}
                @php $pct = $reserva->precio_total > 0 ? min(($totalPagado / $reserva->precio_total) * 100, 100) : 0; @endphp
                <div style="height:6px;background:var(--border);border-radius:4px;margin-bottom:16px;overflow:hidden">
                    <div style="height:100%;width:{{ $pct }}%;background:var(--success);border-radius:4px;transition:width .4s"></div>
                </div>
                @forelse($reserva->pagos as $p)
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:8px 0;border-bottom:1px solid var(--border)">
                    <div>
                        <span class="badge badge-muted">{{ $p->metodo_pago }}</span>
                        <span style="color:var(--muted);font-size:11px;margin-left:6px">{{ $p->fecha_pago->format('d/m/Y H:i') }}</span>
                    </div>
                    <span style="font-weight:500">S/ {{ number_format($p->monto, 2) }}</span>
                </div>
                @empty
                <p style="color:var(--muted);font-size:13px;text-align:center;padding:12px 0">Sin pagos registrados</p>
                @endforelse
            </div>
        </div>

        {{-- Boleta --}}
        @if($reserva->boleta)
        <div class="card" style="border-color:rgba(16,185,129,.3)">
            <div class="card-header" style="background:rgba(16,185,129,.05)">
                <span class="card-title"><i class="bi bi-receipt" style="color:#34d399"></i> Boleta Emitida</span>
                <a href="{{ route('recepcionista.boletas.show', $reserva->boleta) }}" class="btn btn-ghost btn-sm">Ver boleta</a>
            </div>
            <div class="card-body">
                <div style="font-size:13px;color:var(--muted)">Emitida el {{ $reserva->boleta->fecha_emision->format('d/m/Y H:i') }}</div>
                <div style="font-size:18px;font-weight:600;color:#34d399;margin-top:4px">S/ {{ number_format($reserva->boleta->total, 2) }}</div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection