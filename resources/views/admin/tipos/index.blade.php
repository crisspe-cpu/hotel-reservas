@extends('layouts.app')

@section('title', 'Tipos de Habitación')
@section('page-title', 'Tipos de Habitación')
@section('breadcrumb', 'Administración / Tipos')

@section('topbar-actions')
    <a href="{{ route('admin.tipos.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nuevo Tipo</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="bi bi-tags" style="color:var(--accent2)"></i> Tipos de Habitación</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>#</th><th>Nombre</th><th>Capacidad</th><th>Precio Base</th><th>Habitaciones</th><th>Descripción</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                @forelse($tipos as $t)
                <tr>
                    <td class="mono" style="color:var(--muted)">{{ $t->id_tipo }}</td>
                    <td style="font-weight:600">{{ $t->nombre }}</td>
                    <td style="text-align:center">{{ $t->capacidad }} <i class="bi bi-person" style="color:var(--muted);font-size:11px"></i></td>
                    <td style="font-weight:500">S/ {{ number_format($t->precio_base, 2) }}</td>
                    <td style="text-align:center"><span class="badge badge-info">{{ $t->habitaciones_count }}</span></td>
                    <td style="color:var(--muted); font-size:12px; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap">{{ $t->descripcion ?? '—' }}</td>
                    <td>
                        <div class="gap-2">
                            <a href="{{ route('admin.tipos.edit', $t) }}" class="btn btn-ghost btn-sm btn-icon"><i class="bi bi-pencil"></i></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:40px">Sin tipos registrados</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection