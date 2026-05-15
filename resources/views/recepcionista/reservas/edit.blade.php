@extends('layouts.app')

@section('title', 'Editar Reserva')
@section('page-title', 'Editar Reserva')
@section('breadcrumb', 'Inicio / Reservas / Editar')

@section('topbar-actions')
    <a href="{{ route('recepcionista.reservas.show', $reserva) }}" class="btn btn-ghost">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
@endsection

@section('content')

@if ($errors->any())
<div class="card mb-4" style="border-left:4px solid var(--danger)">
    <div class="card-body">
        <ul style="margin:0;padding-left:18px;color:var(--danger)">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<form method="POST" action="{{ route('recepcionista.reservas.update', $reserva) }}">
    @csrf
    @method('PUT')

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px">

        {{-- INFORMACIÓN PRINCIPAL --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">
                    <i class="bi bi-pencil-square" style="color:var(--accent2)"></i>
                    Datos de la Reserva
                </span>
            </div>

            <div class="card-body" style="display:grid;gap:18px">

                {{-- Cliente --}}
                <div>
                    <label>Cliente</label>
                    <input type="text"
                           value="{{ $reserva->cliente->nombre }} {{ $reserva->cliente->apellido }}"
                           disabled>
                </div>

                {{-- Habitación --}}
                <div>
                    <label>Habitación</label>
                    <input type="text"
                           value="@foreach($reserva->habitaciones as $h) Habitación {{ $h->numero }} - {{ $h->tipo->nombre }} @endforeach"
                           disabled>
                </div>

                {{-- Fechas --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div>
                        <label>Fecha Entrada</label>
                        <input type="date"
                               name="fecha_entrada"
                               value="{{ old('fecha_entrada', $reserva->fecha_entrada->format('Y-m-d')) }}"
                               required>
                    </div>

                    <div>
                        <label>Fecha Salida</label>
                        <input type="date"
                               name="fecha_salida"
                               value="{{ old('fecha_salida', $reserva->fecha_salida->format('Y-m-d')) }}"
                               required>
                    </div>
                </div>

                {{-- Huéspedes --}}
                <div>
                    <label>Número de Huéspedes</label>
                    <input type="number"
                           name="num_huespedes"
                           min="1"
                           value="{{ old('num_huespedes', $reserva->num_huespedes) }}"
                           required>
                </div>

                {{-- Estado --}}
                <div>
                    <label>Estado</label>

                    <select name="estado" required>
                        <option value="pendiente"
                            {{ old('estado', $reserva->estado) == 'pendiente' ? 'selected' : '' }}>
                            Pendiente
                        </option>

                        <option value="confirmada"
                            {{ old('estado', $reserva->estado) == 'confirmada' ? 'selected' : '' }}>
                            Confirmada
                        </option>

                        <option value="cancelada"
                            {{ old('estado', $reserva->estado) == 'cancelada' ? 'selected' : '' }}>
                            Cancelada
                        </option>
                    </select>
                </div>

            </div>
        </div>

        {{-- PANEL LATERAL --}}
        <div style="display:grid;gap:20px">

            {{-- RESUMEN --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title">
                        <i class="bi bi-receipt"></i>
                        Resumen
                    </span>
                </div>

                <div class="card-body" style="display:grid;gap:14px">

                    <div>
                        <div style="font-size:12px;color:var(--muted)">Código Reserva</div>
                        <div class="mono">#{{ $reserva->id_reserva }}</div>
                    </div>

                    <div>
                        <div style="font-size:12px;color:var(--muted)">Total</div>
                        <div style="font-size:24px;font-weight:700">
                            S/ {{ number_format($reserva->precio_total, 2) }}
                        </div>
                    </div>

                    <div>
                        <div style="font-size:12px;color:var(--muted)">Noches</div>
                        <div>{{ $reserva->noches }}</div>
                    </div>

                    <div>
                        <div style="font-size:12px;color:var(--muted)">Registrado por</div>
                        <div>{{ $reserva->usuario->name }}</div>
                    </div>

                </div>
            </div>

            {{-- ACCIONES --}}
            <div class="card">
                <div class="card-body" style="display:grid;gap:12px">

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i>
                        Guardar Cambios
                    </button>

                    <a href="{{ route('recepcionista.reservas.show', $reserva) }}"
                       class="btn btn-ghost">
                        Cancelar
                    </a>

                </div>
            </div>

        </div>

    </div>
</form>

@endsection