@extends('layouts.app')

@section('title', 'Editar Usuario')
@section('page-title', 'Editar Usuario')
@section('breadcrumb', 'Inicio / Usuarios / Editar')

@section('topbar-actions')
    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-ghost">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
@endsection

@section('content')

{{-- ERRORES --}}
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

<form method="POST" action="{{ route('admin.usuarios.update', $usuario) }}">
    @csrf
    @method('PUT')

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px">

        {{-- ================= FORMULARIO ================= --}}
        <div class="card">

            <div class="card-header">
                <span class="card-title">
                    <i class="bi bi-pencil-square" style="color:var(--accent2)"></i>
                    Información del Usuario
                </span>
            </div>

            <div class="card-body" style="display:grid;gap:18px">

                {{-- NOMBRE --}}
                <div>
                    <label>Nombre</label>
                    <input type="text"
                           name="name"
                           value="{{ old('name', $usuario->name) }}"
                           required>
                </div>

                {{-- EMAIL --}}
                <div>
                    <label>Email</label>
                    <input type="email"
                           name="email"
                           value="{{ old('email', $usuario->email) }}"
                           required>
                </div>

                {{-- ROL + ESTADO --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

                    <div>
                        <label>Rol</label>
                        <select name="role" required>
                            @foreach($roles as $r)
                                <option value="{{ $r }}"
                                    @selected(old('role', $usuario->role) == $r)>
                                    {{ ucfirst($r) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label>Estado</label>
                        <select name="estado" required>
                            @foreach($estados as $e)
                                <option value="{{ $e }}"
                                    @selected(old('estado', $usuario->estado) == $e)>
                                    {{ ucfirst($e) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                {{-- PASSWORD --}}
                <div style="position:relative">
                    <label>Nueva contraseña (opcional)</label>

                    <input type="password"
                           name="password"
                           id="password"
                           autocomplete="new-password">

                    <button type="button"
                            onclick="togglePassword('password')"
                            style="position:absolute;right:10px;top:35px;background:none;border:none;cursor:pointer">
                        👁️
                    </button>
                </div>

                {{-- CONFIRM PASSWORD --}}
                <div style="position:relative">
                    <label>Confirmar contraseña</label>

                    <input type="password"
                           name="password_confirmation"
                           id="password_confirmation"
                           autocomplete="new-password">

                    <button type="button"
                            onclick="togglePassword('password_confirmation')"
                            style="position:absolute;right:10px;top:35px;background:none;border:none;cursor:pointer">
                        👁️
                    </button>
                </div>

            </div>
        </div>

        {{-- ================= PANEL LATERAL ================= --}}
        <div style="display:grid;gap:20px">

            {{-- RESUMEN --}}
            <div class="card">

                <div class="card-header">
                    <span class="card-title">
                        <i class="bi bi-person-vcard"></i>
                        Resumen
                    </span>
                </div>

                <div class="card-body" style="display:grid;gap:14px">

                    <div>
                        <div style="font-size:12px;color:var(--muted)">ID Usuario</div>
                        <div class="mono">#{{ $usuario->id }}</div>
                    </div>

                    <div>
                        <div style="font-size:12px;color:var(--muted)">Usuario</div>
                        <div style="font-weight:600">{{ $usuario->name }}</div>
                    </div>

                    <div>
                        <div style="font-size:12px;color:var(--muted)">Correo</div>
                        <div class="mono">{{ $usuario->email }}</div>
                    </div>

                    <div>
                        <div style="font-size:12px;color:var(--muted)">Rol</div>
                        <div>
                            @if($usuario->role === 'admin')
                                <span class="badge badge-warning">
                                    <i class="bi bi-shield"></i> Admin
                                </span>
                            @else
                                <span class="badge badge-info">
                                    <i class="bi bi-person"></i> Recepcionista
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div style="font-size:12px;color:var(--muted)">Estado</div>
                        <div>
                            <span class="badge badge-{{ $usuario->estado === 'activo' ? 'success' : 'danger' }}">
                                {{ $usuario->estado }}
                            </span>
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

                    <a href="{{ route('admin.usuarios.index') }}"
                       class="btn btn-ghost">
                        Cancelar
                    </a>

                </div>

            </div>

        </div>

    </div>
</form>

<script>
    function togglePassword(id) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
    }
</script>

@endsection