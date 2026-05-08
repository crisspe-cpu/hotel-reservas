@extends('layouts.app')

@section('title', isset($cliente) ? 'Editar Cliente' : 'Nuevo Cliente')
@section('page-title', isset($cliente) ? 'Editar Cliente' : 'Nuevo Cliente')
@section('breadcrumb', 'Clientes / ' . (isset($cliente) ? 'Editar' : 'Nuevo'))

@section('content')
<div style="max-width:660px">
    <div class="card">
        <div class="card-header">
            <span class="card-title">
                <i class="bi bi-person-badge" style="color:var(--accent2)"></i>
                {{ isset($cliente) ? 'Editar datos del cliente' : 'Registrar nuevo cliente' }}
            </span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ isset($cliente) ? route('recepcionista.clientes.update', $cliente) : route('recepcionista.clientes.store') }}">
                @csrf
                @if(isset($cliente)) @method('PUT') @endif

                <div class="form-grid form-grid-2">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $cliente->nombre ?? '') }}" required placeholder="Ej: Carlos">
                        @error('nombre')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Apellido</label>
                        <input type="text" name="apellido" value="{{ old('apellido', $cliente->apellido ?? '') }}" required placeholder="Ej: Mamani">
                        @error('apellido')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-grid form-grid-2 mt-4">
                    <div class="form-group">
                        <label>Tipo de Documento</label>
                        <select name="tipo_documento" required>
                            <option value="">Seleccionar...</option>
                            @foreach(['dni' => 'DNI', 'pasaporte' => 'Pasaporte', 'otro' => 'Otro'] as $val => $lbl)
                                <option value="{{ $val }}" {{ old('tipo_documento', $cliente->tipo_documento ?? '') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                        @error('tipo_documento')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>N° Documento</label>
                        <input type="text" name="documento" value="{{ old('documento', $cliente->documento ?? '') }}" required placeholder="Ej: 12345678">
                        @error('documento')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-grid form-grid-2 mt-4">
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono', $cliente->telefono ?? '') }}" placeholder="Ej: 987654321">
                        @error('telefono')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>País</label>
                        <input type="text" name="pais" value="{{ old('pais', $cliente->pais ?? '') }}" placeholder="Ej: Perú">
                        @error('pais')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-actions mt-4">
                    <a href="{{ route('recepcionista.clientes.index') }}" class="btn btn-ghost">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i>
                        {{ isset($cliente) ? 'Guardar cambios' : 'Registrar cliente' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection