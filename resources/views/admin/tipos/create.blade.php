@extends('layouts.app')

@section('title', isset($tipo) ? 'Editar Tipo' : 'Nuevo Tipo')
@section('page-title', isset($tipo) ? 'Editar Tipo' : 'Nuevo Tipo de Habitación')
@section('breadcrumb', 'Tipos / ' . (isset($tipo) ? 'Editar' : 'Nuevo'))

@section('content')
<div style="max-width:520px">
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="bi bi-tags" style="color:var(--accent2)"></i>
            {{ isset($tipo) ? 'Editar: '.$tipo->nombre : 'Registrar tipo de habitación' }}
        </span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ isset($tipo) ? route('admin.tipos.update', $tipo) : route('admin.tipos.store') }}">
            @csrf
            @if(isset($tipo)) @method('PUT') @endif

            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre', $tipo->nombre ?? '') }}" required placeholder="Ej: Suite Presidencial">
                @error('nombre')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-grid form-grid-2 mt-4">
                <div class="form-group">
                    <label>Capacidad (personas)</label>
                    <input type="number" name="capacidad" value="{{ old('capacidad', $tipo->capacidad ?? '') }}" min="1" max="10" required placeholder="Ej: 2">
                    @error('capacidad')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Precio base (S/ por noche)</label>
                    <input type="number" name="precio_base" value="{{ old('precio_base', $tipo->precio_base ?? '') }}" step="0.01" min="0" required placeholder="Ej: 180.00">
                    @error('precio_base')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group mt-4">
                <label>Descripción</label>
                <textarea name="descripcion" placeholder="Describe las características de este tipo de habitación...">{{ old('descripcion', $tipo->descripcion ?? '') }}</textarea>
                @error('descripcion')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.tipos.index') }}" class="btn btn-ghost">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i>
                    {{ isset($tipo) ? 'Guardar cambios' : 'Registrar tipo' }}
                </button>
            </div>
        </form>
    </div>
</div>
</div>
@endsection