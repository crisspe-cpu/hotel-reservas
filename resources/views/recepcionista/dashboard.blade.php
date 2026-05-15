@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Bienvenido, ' . auth()->user()->name)

@section('content')

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(108,99,255,.15);color:#a78bfa"><i class="bi bi-calendar-check"></i></div>
        <div>
            <div class="stat-label">Reservas activas</div>
            <div class="stat-value">{{ \App\Models\Reserva::whereIn('estado',['pendiente','confirmada'])->count() }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(16,185,129,.15);color:#34d399"><i class="bi bi-door-open"></i></div>
        <div>
            <div class="stat-label">Disponibles</div>
            <div class="stat-value">{{ \App\Models\Habitacion::where('estado','disponible')->count() }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,.15);color:#fbbf24"><i class="bi bi-clock-history"></i></div>
        <div>
            <div class="stat-label">Pendientes de pago</div>
            <div class="stat-value">{{ \App\Models\Reserva::where('estado','pendiente')->count() }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(59,130,246,.15);color:#60a5fa"><i class="bi bi-people"></i></div>
        <div>
            <div class="stat-label">Clientes</div>
            <div class="stat-value">{{ \App\Models\Cliente::count() }}</div>
        </div>
    </div>
</div>

{{-- Acciones rápidas --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px">
    <a href="{{ route('recepcionista.reservas.create') }}" style="text-decoration:none">
        <div class="card" style="padding:20px; display:flex; align-items:center; gap:14px; cursor:pointer; transition:border-color .15s; border-color:rgba(108,99,255,.3)">
            <div style="width:44px;height:44px;border-radius:12px;background:rgba(108,99,255,.15);display:flex;align-items:center;justify-content:center;font-size:20px;color:var(--accent2)"><i class="bi bi-calendar-plus"></i></div>
            <div>
                <div style="font-weight:600">Nueva Reserva</div>
                <div style="font-size:12px;color:var(--muted)">Registrar reserva de habitación</div>
            </div>
        </div>
    </a>
    <a href="{{ route('recepcionista.clientes.create') }}" style="text-decoration:none">
        <div class="card" style="padding:20px; display:flex; align-items:center; gap:14px; cursor:pointer; transition:border-color .15s">
            <div style="width:44px;height:44px;border-radius:12px;background:rgba(16,185,129,.15);display:flex;align-items:center;justify-content:center;font-size:20px;color:#34d399"><i class="bi bi-person-plus"></i></div>
            <div>
                <div style="font-weight:600">Nuevo Cliente</div>
                <div style="font-size:12px;color:var(--muted)">Registrar huésped en el sistema</div>
            </div>
        </div>
    </a>
</div>

{{-- Reservas de hoy --}}
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="bi bi-calendar-day" style="color:var(--accent2)"></i> Check-in hoy — {{ now()->format('d/m/Y') }}</span>
        <a href="{{ route('recepcionista.reservas.index', ['fecha' => today()->format('Y-m-d')]) }}" class="btn btn-ghost btn-sm">Ver todas</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>#</th><th>Cliente</th><th>Habitación</th><th>Salida</th><th>Estado</th><th>Total</th><th></th></tr>
            </thead>
            <tbody>
                @php
                    $hoy = \App\Models\Reserva::with(['cliente','habitaciones.tipo'])
                        ->whereDate('fecha_entrada', today())
                        ->whereIn('estado', ['pendiente','confirmada'])
                        ->latest()->take(8)->get();
                @endphp
                @forelse($hoy as $r)
                <tr>
                    <td class="mono" style="color:var(--muted)">{{ $r->id_reserva }}</td>
                    <td><div style="font-weight:500">{{ $r->cliente->nombre }} {{ $r->cliente->apellido }}</div></td>
                    <td>@foreach($r->habitaciones as $h)<span class="badge badge-info">{{ $h->numero }}</span> @endforeach</td>
                    <td class="mono">{{ $r->fecha_salida->format('d/m/Y') }}</td>
                    <td>
                        @php $map = ['confirmada'=>'success','pendiente'=>'warning']; @endphp
                        <span class="badge badge-{{ $map[$r->estado] ?? 'muted' }}">{{ $r->estado }}</span>
                    </td>
                    <td style="font-weight:500">S/ {{ number_format($r->precio_total, 2) }}</td>
                    <td><a href="{{ route('recepcionista.reservas.show', $r) }}" class="btn btn-ghost btn-sm btn-icon"><i class="bi bi-eye"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:32px">No hay check-ins para hoy</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection