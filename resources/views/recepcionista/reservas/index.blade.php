@extends('layouts.app')

@section('title', 'Reservas')
@section('page-title', 'Reservas')
@section('breadcrumb', 'Inicio / Reservas')

@section('topbar-actions')
    <a href="{{ route('recepcionista.reservas.create') }}" class="btn btn-primary">
        <i class="bi bi-calendar-plus"></i> Nueva Reserva
    </a>
@endsection

@section('content')

{{-- Filtros --}}
<div class="card mb-4">
    <div class="card-body" style="padding:14px 20px">

        <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center">

            <select name="estado" style="width:auto">
                <option value="">Todos los estados</option>

                @foreach(['pendiente','confirmada','cancelada','finalizada'] as $e)

                    <option 
                        value="{{ $e }}"
                        {{ request('estado') == $e ? 'selected' : '' }}
                    >
                        {{ ucfirst($e) }}
                    </option>

                @endforeach
            </select>

            <input 
                type="date" 
                name="fecha" 
                value="{{ request('fecha') }}" 
                style="width:auto"
            >

            <button type="submit" class="btn btn-ghost">
                <i class="bi bi-funnel"></i> Filtrar
            </button>

            @if(request()->hasAny(['estado','fecha']))
                <a href="{{ route('recepcionista.reservas.index') }}" class="btn btn-ghost">
                    <i class="bi bi-x"></i> Limpiar
                </a>
            @endif

        </form>

    </div>
</div>

<div class="card">

    <div class="card-header">

        <span class="card-title">
            <i class="bi bi-calendar-check" style="color:var(--accent2)"></i>
            Lista de Reservas
        </span>

        <span class="badge badge-muted">
            {{ $reservas->total() }} registros
        </span>

    </div>

    <div class="table-wrap">

        <table>

            <thead>
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Habitación</th>
                    <th>Entrada</th>
                    <th>Salida</th>
                    <th>Huéspedes</th>
                    <th>Estado</th>
                    <th>Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>

                @forelse($reservas as $r)

                <tr>

                    <td class="mono" style="color:var(--muted)">
                        {{ $r->id_reserva }}
                    </td>

                    <td>

                        <div style="font-weight:500">
                            {{ $r->cliente->nombre }}
                            {{ $r->cliente->apellido }}
                        </div>

                        <div style="font-size:11px;color:var(--muted)">
                            {{ $r->cliente->documento }}
                        </div>

                    </td>

                    <td>

                        @foreach($r->habitaciones as $h)

                            <span class="badge badge-info">
                                {{ $h->numero }}
                            </span>

                        @endforeach

                    </td>

                    <td class="mono">
                        {{ $r->fecha_entrada->format('d/m/Y') }}
                    </td>

                    <td class="mono">
                        {{ $r->fecha_salida->format('d/m/Y') }}
                    </td>

                    <td style="text-align:center">
                        {{ $r->num_huespedes }}
                    </td>

                    <td>

                        @php
                            $map = [
                                'confirmada' => 'success',
                                'pendiente'  => 'warning',
                                'cancelada'  => 'danger',
                                'finalizada' => 'info'
                            ];
                        @endphp

                        <span class="badge badge-{{ $map[$r->estado] ?? 'muted' }}">
                            {{ ucfirst($r->estado) }}
                        </span>

                    </td>

                    <td style="font-weight:500">
                        S/ {{ number_format($r->precio_total, 2) }}
                    </td>

                    <td>

                        <div class="gap-2">

                            {{-- Ver --}}
                            <a 
                                href="{{ route('recepcionista.reservas.show', $r) }}"
                                class="btn btn-ghost btn-sm btn-icon"
                                title="Ver"
                            >
                                <i class="bi bi-eye"></i>
                            </a>

                            {{-- Editar solo si NO está cancelada o finalizada --}}
                            @if(!in_array($r->estado, ['cancelada', 'finalizada']))

                                <a 
                                    href="{{ route('recepcionista.reservas.edit', $r) }}"
                                    class="btn btn-ghost btn-sm btn-icon"
                                    title="Editar"
                                >
                                    <i class="bi bi-pencil"></i>
                                </a>

                            @endif

                        </div>

                    </td>

                </tr>

                @empty

                <tr>

                    <td colspan="9" style="text-align:center;color:var(--muted);padding:40px">
                        No hay reservas registradas
                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div style="padding:14px 20px">
        {{ $reservas->withQueryString()->links() }}
    </div>

</div>

@endsection