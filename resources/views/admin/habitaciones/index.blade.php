@extends('layouts.app')

@section('title', 'Habitaciones')
@section('page-title', 'Habitaciones')
@section('breadcrumb', 'Administración / Habitaciones')

@section('topbar-actions')
    <a href="{{ route('admin.habitaciones.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nueva Habitación</a>
@endsection

@section('content')

{{-- Stats rápidas --}}
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:20px">
    @php
        $total = $habitaciones->total();
        $disp  = $habitaciones->getCollection()->where('estado','disponible')->count();
        $ocup  = $habitaciones->getCollection()->where('estado','no disponible')->count();
    @endphp
    <div class="stat-card" style="padding:14px 16px">
        <div class="stat-icon" style="background:rgba(16,185,129,.15);color:#34d399;width:32px;height:32px;font-size:14px"><i class="bi bi-check-circle"></i></div>
        <div><div class="stat-label">Disponibles</div><div class="stat-value" style="font-size:20px">{{ $disp }}</div></div>
    </div>
    <div class="stat-card" style="padding:14px 16px">
        <div class="stat-icon" style="background:rgba(245,158,11,.15);color:#fbbf24;width:32px;height:32px;font-size:14px"><i class="bi bi-person-fill"></i></div>
        <div><div class="stat-label">Ocupadas</div><div class="stat-value" style="font-size:20px">{{ $ocup }}</div></div>
    </div>
    <div class="stat-card" style="padding:14px 16px">
        <div class="stat-icon" style="background:rgba(239,68,68,.15);color:#f87171;width:32px;height:32px;font-size:14px"><i class="bi bi-tools"></i></div>
        <div><div class="stat-label">Mantenimiento</div><div class="stat-value" style="font-size:20px">{{ $habitaciones->getCollection()->where('estado','mantenimiento')->count() }}</div></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="bi bi-door-open" style="color:var(--accent2)"></i> Lista de Habitaciones</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>N°</th><th>Piso</th><th>Tipo</th><th>Capacidad</th><th>Precio/noche</th><th>Estado</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                @forelse($habitaciones as $h)
                <tr>
                    <td style="font-weight:600; font-size:15px">{{ $h->numero }}</td>
                    <td style="color:var(--muted)">{{ $h->piso }}°</td>
                    <td>{{ $h->tipo->nombre }}</td>
                    <td style="text-align:center">{{ $h->tipo->capacidad }} <i class="bi bi-person" style="color:var(--muted);font-size:11px"></i></td>
                    <td style="font-weight:500">S/ {{ number_format($h->tipo->precio_base, 2) }}</td>
                    <td>
                        @php $badges = ['disponible'=>'success','no disponible'=>'warning','mantenimiento'=>'danger']; @endphp
                        <span class="badge badge-{{ $badges[$h->estado] ?? 'muted' }}">{{ $h->estado }}</span>
                    </td>
                    <td>
                        <div class="gap-2">
                            <a href="{{ route('admin.habitaciones.edit', $h) }}" class="btn btn-ghost btn-sm btn-icon"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('admin.habitaciones.destroy', $h) }}" onsubmit="return confirm('¿Eliminar habitación?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm btn-icon"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:40px">Sin habitaciones registradas</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:14px 20px">{{ $habitaciones->links() }}</div>
</div>
@endsection