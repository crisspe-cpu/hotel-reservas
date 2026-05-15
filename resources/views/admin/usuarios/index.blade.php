@extends('layouts.app')

@section('title', 'Usuarios')
@section('page-title', 'Usuarios del Sistema')
@section('breadcrumb', 'Administración / Usuarios')

@section('topbar-actions')
    <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus"></i> Nuevo Usuario
    </a>
@endsection

@section('content')

{{-- CARD PRINCIPAL --}}
<div class="card">

    {{-- HEADER --}}
    <div class="card-header">
        <span class="card-title">
            <i class="bi bi-shield-lock" style="color:var(--accent2)"></i>
            Usuarios del Sistema
        </span>

        <span class="badge badge-muted">
            {{ $usuarios->total() }} registros
        </span>
    </div>

    {{-- TABLA --}}
    <div class="card-body" style="padding:0">

        <div class="table-wrap">

            <table>

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Usuario</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Creado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($usuarios as $u)

                        <tr>

                            {{-- ID --}}
                            <td class="mono" style="color:var(--muted)">
                                {{ $u->id }}
                            </td>

                            {{-- USUARIO --}}
                            <td>
                                <div style="display:flex;align-items:center;gap:10px">

                                    <div style="width:32px;height:32px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;flex-shrink:0">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>

                                    <div>
                                        <div style="font-weight:500">
                                            {{ $u->name }}
                                        </div>

                                        <div style="font-size:11px;color:var(--muted)">
                                            {{ $u->telefono ?? '—' }}
                                        </div>
                                    </div>

                                </div>
                            </td>

                            {{-- EMAIL --}}
                            <td style="color:var(--muted);font-size:13px">
                                {{ $u->email }}
                            </td>

                            {{-- ROL --}}
                            <td>
                                @if($u->role === 'admin')
                                    <span class="badge badge-warning">
                                        <i class="bi bi-shield"></i> Admin
                                    </span>
                                @else
                                    <span class="badge badge-info">
                                        <i class="bi bi-person"></i> Recepcionista
                                    </span>
                                @endif
                            </td>

                            {{-- ESTADO --}}
                            <td>
                                <span class="badge badge-{{ $u->estado === 'activo' ? 'success' : 'danger' }}">
                                    {{ $u->estado }}
                                </span>
                            </td>

                            {{-- FECHA --}}
                            <td class="mono" style="font-size:12px;color:var(--muted)">
                                {{ optional($u->created_at)->format('d/m/Y') }}
                            </td>

                            {{-- ACCIONES --}}
                            <td>
                                <div style="display:flex;gap:8px;align-items:center">

                                    <a href="{{ route('admin.usuarios.edit', $u) }}"
                                    class="btn btn-ghost btn-sm btn-icon"
                                    title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    @if($u->id !== auth()->id())
                                        <form method="POST"
                                            action="{{ route('admin.usuarios.destroy', $u) }}"
                                            onsubmit="return confirm('¿Desactivar este usuario?')">

                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-danger btn-sm btn-icon"
                                                    title="Eliminar">
                                                <i class="bi bi-person-x"></i>
                                            </button>

                                        </form>
                                    @endif

                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7"
                                style="text-align:center;color:var(--muted);padding:40px">
                                Sin usuarios registrados
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    {{-- PAGINACIÓN --}}
    <div style="padding:14px 20px">
        {{ $usuarios->links() }}
    </div>

</div>

@endsection