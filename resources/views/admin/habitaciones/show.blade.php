@extends('layouts.app')

@section('title', 'Detalle Habitación')
@section('page-title', 'Detalle Habitación')
@section('breadcrumb', 'Administración / Habitaciones / Ver')

@section('topbar-actions')
    <a href="{{ route('admin.habitaciones.index') }}" class="btn btn-ghost">
        <i class="bi bi-arrow-left"></i> Volver
    </a>

    <a href="{{ route('admin.habitaciones.edit', $habitacion) }}" class="btn btn-primary">
        <i class="bi bi-pencil"></i> Editar
    </a>
@endsection

@section('content')

<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px">

    {{-- INFORMACIÓN --}}
    <div class="card">

        <div class="card-header">
            <span class="card-title">
                <i class="bi bi-door-open" style="color:var(--accent2)"></i>
                Información de la Habitación
            </span>
        </div>

        <div class="card-body" style="display:grid;gap:18px">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

                <div>
                    <label>Número</label>
                    <input type="text"
                           value="{{ $habitacion->numero }}"
                           disabled>
                </div>

                <div>
                    <label>Piso</label>
                    <input type="text"
                           value="{{ $habitacion->piso }}° Piso"
                           disabled>
                </div>

            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

                <div>
                    <label>Tipo</label>
                    <input type="text"
                           value="{{ $habitacion->tipo->nombre }}"
                           disabled>
                </div>

                <div>
                    <label>Capacidad</label>
                    <input type="text"
                           value="{{ $habitacion->tipo->capacidad }} huéspedes"
                           disabled>
                </div>

            </div>

            <div>

                <label>Descripción</label>

                <textarea rows="4" disabled>{{ $habitacion->tipo->descripcion ?: 'Sin descripción registrada.' }}</textarea>

            </div>

        </div>

    </div>

    {{-- PANEL --}}
    <div style="display:grid;gap:20px">

        {{-- RESUMEN --}}
        <div class="card">

            <div class="card-header">
                <span class="card-title">
                    <i class="bi bi-info-circle"></i>
                    Resumen
                </span>
            </div>

            <div class="card-body" style="display:grid;gap:14px">

                <div>
                    <div style="font-size:12px;color:var(--muted)">
                        Estado
                    </div>

                    @php
                        $badges = [
                            'disponible' => 'success',
                            'no disponible' => 'warning',
                            'mantenimiento' => 'danger'
                        ];
                    @endphp

                    <span class="badge badge-{{ $badges[$habitacion->estado] ?? 'muted' }}">
                        {{ $habitacion->estado }}
                    </span>
                </div>

                <div>
                    <div style="font-size:12px;color:var(--muted)">
                        Precio por noche
                    </div>

                    <div style="font-size:24px;font-weight:700">
                        S/ {{ number_format($habitacion->tipo->precio_base, 2) }}
                    </div>
                </div>

                <div>
                    <div style="font-size:12px;color:var(--muted)">
                        Reservas asociadas
                    </div>

                    <div>
                        {{ $habitacion->reservas->count() }}
                    </div>
                </div>

            </div>

        </div>

        {{-- RESERVAS --}}
        <div class="card">

            <div class="card-header">
                <span class="card-title">
                    <i class="bi bi-calendar-check"></i>
                    Últimas Reservas
                </span>
            </div>

            <div class="card-body">

                @forelse($habitacion->reservas->take(5) as $r)

                    <div style="padding:10px 0;border-bottom:1px solid var(--border)">

                        <div style="font-weight:600">
                            {{ $r->cliente->nombre }} {{ $r->cliente->apellido }}
                        </div>

                        <div style="font-size:12px;color:var(--muted)">
                            {{ $r->fecha_entrada->format('d/m/Y') }}
                            →
                            {{ $r->fecha_salida->format('d/m/Y') }}
                        </div>

                    </div>

                @empty

                    <div style="color:var(--muted)">
                        Sin reservas registradas.
                    </div>

                @endforelse

            </div>

        </div>

    </div>

</div>

@endsection