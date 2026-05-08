@extends('layouts.app')

@section('title', isset($usuario) ? 'Editar Usuario' : 'Nuevo Usuario')
@section('page-title', isset($usuario) ? 'Editar Usuario' : 'Nuevo Usuario')
@section('breadcrumb', 'Usuarios / ' . (isset($usuario) ? 'Editar' : 'Nuevo'))

@section('content')
<div style="max-width:600px">
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="bi bi-person-badge" style="color:var(--accent2)"></i>
            {{ isset($usuario) ? 'Editar: '.$usuario->name : 'Registrar nuevo usuario' }}
        </span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ isset($usuario) ? route('admin.usuarios.update', $usuario) : route('admin.usuarios.store') }}">
            @csrf
            @if(isset($usuario)) @method('PUT') @endif

            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" name="name" value="{{ old('name', $usuario->name ?? '') }}" required>
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                {{-- <div class="form-group">
                    <label>Apellido</label>
                    <input type="text" name="apellido" value="{{ old('apellido', $usuario->apellido ?? '') }}" required>
                    @error('apellido')<div class="form-error">{{ $message }}</div>@enderror
                </div> --}}
            </div>

            <div class="form-grid form-grid-2 mt-4">
                <div class="form-group">
                    <label>Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email', $usuario->email ?? '') }}" required>
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono', $usuario->telefono ?? '') }}" placeholder="Opcional">
                </div>
            </div>

            <div class="form-grid form-grid-2 mt-4">
                <div class="form-group">
                    <label>Rol</label>
                    <select name="role" required>
                        @foreach(['recepcionista' => 'Recepcionista', 'admin' => 'admin'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('role', $usuario->role ?? '') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    @error('role')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                @if(isset($usuario))
                <div class="form-group">
                    <label>Estado</label>
                    <select name="estado" required>
                        <option value="activo"   {{ $usuario->estado == 'activo'   ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ $usuario->estado == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
                @endif
            </div>

            <div class="form-grid form-grid-2 mt-4">
                <div class="form-group">
                    <label>Contraseña {{ isset($usuario) ? '(dejar vacío para no cambiar)' : '' }}</label>
                    <input type="password" name="password" {{ isset($usuario) ? '' : 'required' }} placeholder="Mínimo 8 caracteres">
                    @error('password')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" {{ isset($usuario) ? '' : 'required' }} placeholder="Repetir password">
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-ghost">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i>
                    {{ isset($usuario) ? 'Guardar cambios' : 'Crear usuario' }}
                </button>
            </div>
        </form>
    </div>
</div>
</div>
@endsection