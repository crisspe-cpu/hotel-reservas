@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Inicio / Administración')

@section('content')

<div class="stats-grid">

    <div class="stat-card">

        <div class="stat-icon" style="background:rgba(108,99,255,.15); color:#a78bfa">
            <i class="bi bi-calendar-check"></i>
        </div>

        <div>
            <div class="stat-label">Reservas hoy</div>
            <div class="stat-value">{{ $totalReservasHoy }}</div>
            <div class="stat-sub">nuevas registradas</div>
        </div>

    </div>

    <div class="stat-card">

        <div class="stat-icon" style="background:rgba(16,185,129,.15); color:#34d399">
            <i class="bi bi-door-open"></i>
        </div>

        <div>
            <div class="stat-label">Disponibles</div>
            <div class="stat-value">{{ $habitacionesDisponibles }}</div>
            <div class="stat-sub">de {{ $totalHabitaciones }} habitaciones</div>
        </div>

    </div>

    <div class="stat-card">

        <div class="stat-icon" style="background:rgba(245,158,11,.15); color:#fbbf24">
            <i class="bi bi-people"></i>
        </div>

        <div>
            <div class="stat-label">Clientes</div>
            <div class="stat-value">{{ $totalClientes }}</div>
            <div class="stat-sub">registrados</div>
        </div>

    </div>

    <div class="stat-card">

        <div class="stat-icon" style="background:rgba(16,185,129,.15); color:#34d399">
            <i class="bi bi-cash-coin"></i>
        </div>

        <div>
            <div class="stat-label">Ingresos mes</div>

            <div class="stat-value" style="font-size:18px">
                S/ {{ number_format($ingresosMes, 2) }}
            </div>

            <div class="stat-sub">
                {{ now()->translatedFormat('F Y') }}
            </div>

        </div>

    </div>

</div>

<div style="display:grid; grid-template-columns:2fr 1fr; gap:16px">

    {{-- Reservas activas --}}
    <div class="card">

        <div class="card-header">

            <span class="card-title">
                <i class="bi bi-calendar-week" style="color:var(--accent2)"></i>
                Reservas Activas
            </span>

            <a href="{{ route('recepcionista.reservas.index') }}" class="btn btn-ghost btn-sm">
                Ver todas
            </a>

        </div>

        <div class="table-wrap">

            <table>

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Entrada</th>
                        <th>Salida</th>
                        <th>Estado</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($reservasActivas as $r)

                    <tr>

                        <td class="mono" style="color:var(--muted)">
                            {{ $r->id_reserva }}
                        </td>

                        <td>

                            <div style="font-weight:500">
                                {{ $r->cliente->nombre }}
                                {{ $r->cliente->apellido }}
                            </div>

                            <div style="font-size:11px; color:var(--muted)">
                                {{ $r->cliente->documento }}
                            </div>

                        </td>

                        <td class="mono">
                            {{ $r->fecha_entrada->format('d/m/Y') }}
                        </td>

                        <td class="mono">
                            {{ $r->fecha_salida->format('d/m/Y') }}
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

                    </tr>

                    @empty

                    <tr>

                        <td colspan="6" style="text-align:center; color:var(--muted); padding:32px">
                            Sin reservas activas
                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    {{-- Ocupación por tipo --}}
    <div class="card">

        <div class="card-header">

            <span class="card-title">
                <i class="bi bi-pie-chart" style="color:var(--accent2)"></i>
                Ocupación por Tipo
            </span>

        </div>

        <div class="card-body">

            @forelse($ocupacionPorTipo as $tipo)

            <div style="margin-bottom:14px">

                <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:6px">

                    <span>{{ $tipo->nombre }}</span>

                    <span style="color:var(--accent2); font-weight:500">
                        {{ $tipo->total }}
                    </span>

                </div>

                <div style="height:6px; background:var(--border); border-radius:4px; overflow:hidden">

                    <div
                        style="
                            height:100%;
                            width:{{ min(($tipo->total / max($ocupacionPorTipo->max('total'), 1)) * 100, 100) }}%;
                            background:var(--accent);
                            border-radius:4px;
                            transition:width .4s ease
                        "
                    ></div>

                </div>

            </div>

            @empty

            <p class="text-muted" style="text-align:center; padding:20px 0">
                Sin ocupación registrada
            </p>

            @endforelse

            <div class="divider"></div>

            <div style="display:flex; justify-content:space-between; font-size:13px">

                <span style="color:var(--muted)">
                    Habitaciones ocupadas
                </span>

                <span style="font-weight:600; color:var(--warning)">
                    {{ $habitacionesOcupadas }} / {{ $totalHabitaciones }}
                </span>

            </div>

        </div>

    </div>

</div>

@endsection