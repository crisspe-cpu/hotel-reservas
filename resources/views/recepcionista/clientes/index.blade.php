@extends('layouts.app')

@section('title', 'Clientes')
@section('page-title', 'Clientes')
@section('breadcrumb', 'Inicio / Clientes')

@section('topbar-actions')
    <a href="{{ route('recepcionista.clientes.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus"></i> Nuevo Cliente
    </a>
@endsection

@section('content')

{{-- Buscador --}}
<div class="card mb-4">
    <div class="card-body" style="padding:14px 20px">
        <form method="GET" action="{{ route('recepcionista.clientes.index') }}" style="display:flex; gap:10px">
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por nombre, apellido o documento..." style="max-width:380px">
            <button type="submit" class="btn btn-ghost"><i class="bi bi-search"></i> Buscar</button>
            @if(request('buscar'))
                <a href="{{ route('recepcionista.clientes.index') }}" class="btn btn-ghost"><i class="bi bi-x"></i> Limpiar</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="bi bi-people" style="color:var(--accent2)"></i> Lista de Clientes</span>
        <span class="badge badge-muted">{{ $clientes->total() }} registros</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Documento</th>
                    <th>Teléfono</th>
                    <th>País</th>
                    <th>Registrado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clientes as $c)
                <tr>
                    <td class="mono" style="color:var(--muted)">{{ $c->id_cliente }}</td>
                    <td>
                        <div style="font-weight:500">{{ $c->nombre }} {{ $c->apellido }}</div>
                    </td>
                    <td>
                        <span class="badge badge-muted">{{ strtoupper($c->tipo_documento) }}</span>
                        <span class="mono" style="margin-left:4px">{{ $c->documento }}</span>
                    </td>
                    <td style="color:var(--muted)">{{ $c->telefono ?? '—' }}</td>
                    <td style="color:var(--muted)">{{ $c->pais ?? '—' }}</td>
                    <td class="mono" style="color:var(--muted); font-size:12px">{{ $c->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="gap-2">
                            <a href="{{ route('recepcionista.clientes.show', $c) }}" class="btn btn-ghost btn-sm btn-icon" title="Ver"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('recepcionista.clientes.edit', $c) }}" class="btn btn-ghost btn-sm btn-icon" title="Editar"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('recepcionista.clientes.destroy', $c) }}" onsubmit="return confirm('¿Eliminar este cliente?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Eliminar"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center; color:var(--muted); padding:40px">No se encontraron clientes</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:14px 20px">
        {{ $clientes->withQueryString()->links() }}
    </div>
</div>
@endsection