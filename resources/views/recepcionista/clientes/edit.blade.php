@extends('layouts.app')

@section('title', 'Editar Cliente')
@section('page-title', 'Editar Cliente')
@section('breadcrumb', 'Inicio / Clientes / Editar')

@section('topbar-actions')
    <a href="{{ route('recepcionista.clientes.show', $cliente) }}" class="btn btn-ghost">
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

<form method="POST" action="{{ route('recepcionista.clientes.update', $cliente) }}">
    @csrf
    @method('PUT')

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px">

        {{-- FORMULARIO --}}
        <div class="card">

            <div class="card-header">
                <span class="card-title">
                    <i class="bi bi-pencil-square" style="color:var(--accent2)"></i>
                    Información del Cliente
                </span>
            </div>

            <div class="card-body" style="display:grid;gap:18px">

                {{-- Nombre y apellido --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

                    <div>
                        <label>Nombres</label>

                        <input type="text"
                               name="nombre"
                               value="{{ old('nombre', $cliente->nombre) }}"
                               required>
                    </div>

                    <div>
                        <label>Apellidos</label>

                        <input type="text"
                               name="apellido"
                               value="{{ old('apellido', $cliente->apellido) }}"
                               required>
                    </div>

                </div>

                {{-- Documento --}}
                <div style="display:grid;grid-template-columns:180px 1fr;gap:16px">

                    <div>
                        <label>Tipo Documento</label>

                        <select name="tipo_documento" required>

                            <option value="dni"
                                {{ old('tipo_documento', $cliente->tipo_documento) == 'dni' ? 'selected' : '' }}>
                                DNI
                            </option>

                            <option value="pasaporte"
                                {{ old('tipo_documento', $cliente->tipo_documento) == 'pasaporte' ? 'selected' : '' }}>
                                Pasaporte
                            </option>

                            <option value="otro"
                                {{ old('tipo_documento', $cliente->tipo_documento) == 'otro' ? 'selected' : '' }}>
                                Otro
                            </option>

                        </select>
                    </div>

                    <div>
                        <label>Número Documento</label>

                        <input type="text"
                               name="documento"
                               value="{{ old('documento', $cliente->documento) }}"
                               required>
                    </div>

                </div>

                {{-- Teléfono y país --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

                    <div>
                        <label>Teléfono</label>

                        <input type="text"
                               name="telefono"
                               value="{{ old('telefono', $cliente->telefono) }}">
                    </div>

                    <div>
                        <label>País</label>

                        <input type="text"
                               name="pais"
                               value="{{ old('pais', $cliente->pais) }}">
                    </div>

                </div>

            </div>
        </div>

        {{-- PANEL LATERAL --}}
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
                        <div style="font-size:12px;color:var(--muted)">
                            Código Cliente
                        </div>

                        <div class="mono">
                            #{{ $cliente->id_cliente }}
                        </div>
                    </div>

                    <div>
                        <div style="font-size:12px;color:var(--muted)">
                            Cliente
                        </div>

                        <div style="font-weight:600">
                            {{ $cliente->nombre }} {{ $cliente->apellido }}
                        </div>
                    </div>

                    <div>
                        <div style="font-size:12px;color:var(--muted)">
                            Documento
                        </div>

                        <div class="mono">
                            {{ strtoupper($cliente->tipo_documento) }} - {{ $cliente->documento }}
                        </div>
                    </div>

                    <div>
                        <div style="font-size:12px;color:var(--muted)">
                            Registrado
                        </div>

                        <div>
                            {{ $cliente->created_at->format('d/m/Y') }}
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

                    <a href="{{ route('recepcionista.clientes.show', $cliente) }}"
                       class="btn btn-ghost">
                        Cancelar
                    </a>

                </div>

            </div>

        </div>

    </div>
</form>

@endsection 