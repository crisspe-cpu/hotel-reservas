@extends('layouts.app')

@section('title', isset($habitacion) ? 'Editar Habitación' : 'Nueva Habitación')
@section('page-title', isset($habitacion) ? 'Editar Habitación' : 'Nueva Habitación')
@section('breadcrumb', 'Habitaciones / ' . (isset($habitacion) ? 'Editar' : 'Nueva'))

@section('content')
<div style="max-width:560px">
<div class="card">
    <div class="card-header">
        <span class="card-title">
            <i class="bi bi-door-open" style="color:var(--accent2)"></i>
            {{ isset($habitacion) ? 'Editar Habitación '.$habitacion->numero : 'Registrar nueva habitación' }}
        </span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ isset($habitacion) ? route('admin.habitaciones.update', $habitacion) : route('admin.habitaciones.store') }}">
            @csrf
            @if(isset($habitacion)) @method('PUT') @endif

            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label>Número</label>
                    <input type="text" name="numero" value="{{ old('numero', $habitacion->numero ?? '') }}" required placeholder="Ej: 101">
                    @error('numero')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Piso</label>
                    <input type="number" name="piso" value="{{ old('piso', $habitacion->piso ?? '') }}" min="1" required placeholder="Ej: 1">
                    @error('piso')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-grid form-grid-2 mt-4">
                <div class="form-group">
                    <label>Tipo de Habitación</label>
                    <select name="id_tipo_habitacion" required>
                        <option value="">Seleccionar...</option>
                        @foreach($tipos as $t)
                            <option value="{{ $t->id_tipo }}" {{ old('id_tipo_habitacion', $habitacion->id_tipo_habitacion ?? '') == $t->id_tipo ? 'selected' : '' }}>
                                {{ $t->nombre }} — S/ {{ number_format($t->precio_base, 2) }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_tipo_habitacion')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <select name="estado" required>
                        @foreach(['disponible','no disponible','mantenimiento'] as $e)
                            <option value="{{ $e }}" {{ old('estado', $habitacion->estado ?? 'disponible') == $e ? 'selected' : '' }}>{{ ucfirst($e) }}</option>
                        @endforeach
                    </select>
                    @error('estado')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-actions mt-4">
                <a href="{{ route('admin.habitaciones.index') }}" class="btn btn-ghost">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i>
                    {{ isset($habitacion) ? 'Guardar cambios' : 'Registrar habitación' }}
                </button>
            </div>
        </form>
    </div>
</div>
</div>
@endsection