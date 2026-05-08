@extends('layouts.app')

@section('title', 'Detalle Cliente')
@section('page-title', $cliente->nombre_completo)
@section('breadcrumb', 'Clientes / Detalle')

@section('topbar-actions')
    <a href="{{ route('recepcionista.clientes.edit', $cliente) }}" class="btn btn-ghost"><i class="bi bi-pencil"></i> Editar</a>
    <a href="{{ route('recepcionista.reservas.create', ['id_cliente' => $cliente->id_cliente]) }}" class="btn btn-primary"><i class="bi bi-calendar-plus"></i> Nueva Reserva</a>
@endsection

@section('content')
<div style="display:grid; grid-template-columns:1fr 2fr; gap:16px; align-items:start">

    {{-- Info del cliente --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-person-circle" style="color:var(--accent2)"></i> Información</span>
        </div>
        <div class="card-body">
            <div style="text-align:center; margin-bottom:20px">
                <div style="width:64px;height:64px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;font-size:26px;font-weight:600;margin:0 auto 10px">
                    {{ strtoupper(substr($cliente->nombre, 0, 1)) }}
                </div>
                <div style="font-size:16px;font-weight:600">{{ $cliente->nombre_completo }}</div>
                <div class="badge badge-info" style="margin-top:6px">{{ strtoupper($cliente->tipo_documento) }}</div>
            </div>
            <div class="divider"></div>
            <table style="width:100%; font-size:13px">
                <tr>
                    <td style="color:var(--muted);padding:8px 0">Documento</td>
                    <td class="mono" style="text-align:right">{{ $cliente->documento }}</td>
                </tr>
                <tr>
                    <td style="color:var(--muted);padding:8px 0;border-top:1px solid var(--border)">Teléfono</td>
                    <td style="text-align:right;border-top:1px solid var(--border)">{{ $cliente->telefono ?? '—' }}</td>
                </tr>
                <tr>
                    <td style="color:var(--muted);padding:8px 0;border-top:1px solid var(--border)">País</td>
                    <td style="text-align:right;border-top:1px solid var(--border)">{{ $cliente->pais ?? '—' }}</td>
                </tr>
                <tr>
                    <td style="color:var(--muted);padding:8px 0;border-top:1px solid var(--border)">Registrado</td>
                    <td class="mono" style="font-size:11px;text-align:right;border-top:1px solid var(--border)">{{ $cliente->created_at->format('d/m/Y') }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Historial de reservas --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-clock-history" style="color:var(--accent2)"></i> Últimas Reservas</span>
            <a href="{{ route('recepcionista.reservas.index', ['id_cliente' => $cliente->id_cliente]) }}" class="btn btn-ghost btn-sm">Ver todas</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>#</th><th>Entrada</th><th>Salida</th><th>Noches</th><th>Estado</th><th>Total</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($cliente->reservas as $r)
                    <tr>
                        <td class="mono" style="color:var(--muted)">{{ $r->id_reserva }}</td>
                        <td class="mono">{{ $r->fecha_entrada->format('d/m/Y') }}</td>
                        <td class="mono">{{ $r->fecha_salida->format('d/m/Y') }}</td>
                        <td style="text-align:center">{{ $r->noches }}</td>
                        <td>
                            @php $badges = ['confirmada'=>'success','pendiente'=>'warning','cancelada'=>'danger']; @endphp
                            <span class="badge badge-{{ $badges[$r->estado] ?? 'muted' }}">{{ $r->estado }}</span>
                        </td>
                        <td style="font-weight:500">S/ {{ number_format($r->precio_total, 2) }}</td>
                        <td><a href="{{ route('recepcionista.reservas.show', $r) }}" class="btn btn-ghost btn-sm btn-icon"><i class="bi bi-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:32px">Sin reservas registradas</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection