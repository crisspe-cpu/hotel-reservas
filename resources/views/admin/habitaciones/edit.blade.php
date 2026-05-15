@extends('layouts.app')

@section('title', 'Editar Habitación')
@section('page-title', 'Editar Habitación')
@section('breadcrumb', 'Administración / Habitaciones / Editar')

@section('topbar-actions')
    <a href="{{ route('admin.habitaciones.show', $habitacion) }}" class="btn btn-ghost">
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

<form method="POST"
      action="{{ route('admin.habitaciones.update', $habitacion) }}">

    @csrf
    @method('PUT')

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px">

        {{-- FORMULARIO --}}
        <div class="card">

            <div class="card-header">
                <span class="card-title">
                    <i class="bi bi-pencil-square" style="color:var(--accent2)"></i>
                    Datos de la Habitación
                </span>
            </div>

            <div class="card-body" style="display:grid;gap:18px">

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

                    <div>
                        <label>Número</label>

                        <input type="text"
                               name="numero"
                               value="{{ old('numero', $habitacion->numero) }}"
                               required>
                    </div>

                    <div>
                        <label>Piso</label>

                        <input type="number"
                               name="piso"
                               min="1"
                               value="{{ old('piso', $habitacion->piso) }}"
                               required>
                    </div>

                </div>

                <div>

                    <label>Tipo de Habitación</label>

                    <select name="id_tipo_habitacion" required>

                        @foreach($tipos as $tipo)

                            <option value="{{ $tipo->id_tipo }}"
                                {{ old('id_tipo_habitacion', $habitacion->id_tipo_habitacion) == $tipo->id_tipo ? 'selected' : '' }}>

                                {{ $tipo->nombre }}
                                —
                                Capacidad: {{ $tipo->capacidad }}
                                —
                                S/ {{ number_format($tipo->precio_base, 2) }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div>

                    <label>Estado</label>

                    <select name="estado" required>

                        <option value="disponible"
                            {{ old('estado', $habitacion->estado) == 'disponible' ? 'selected' : '' }}>
                            Disponible
                        </option>

                        <option value="no disponible"
                            {{ old('estado', $habitacion->estado) == 'no disponible' ? 'selected' : '' }}>
                            No Disponible
                        </option>

                        <option value="mantenimiento"
                            {{ old('estado', $habitacion->estado) == 'mantenimiento' ? 'selected' : '' }}>
                            Mantenimiento
                        </option>

                    </select>

                </div>

            </div>

        </div>

        {{-- PANEL --}}
        <div style="display:grid;gap:20px">

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
                            Habitación
                        </div>

                        <div style="font-size:24px;font-weight:700">
                            #{{ $habitacion->numero }}
                        </div>
                    </div>

                    <div>
                        <div style="font-size:12px;color:var(--muted)">
                            Tipo actual
                        </div>

                        <div>
                            {{ $habitacion->tipo->nombre }}
                        </div>
                    </div>

                    <div>
                        <div style="font-size:12px;color:var(--muted)">
                            Precio actual
                        </div>

                        <div>
                            S/ {{ number_format($habitacion->tipo->precio_base, 2) }}
                        </div>
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

                    <a href="{{ route('admin.habitaciones.show', $habitacion) }}"
                       class="btn btn-ghost">
                        Cancelar
                    </a>

                </div>

            </div>

        </div>

    </div>

</form>

@endsection