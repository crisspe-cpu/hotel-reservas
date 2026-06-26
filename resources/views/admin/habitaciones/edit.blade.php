@extends('layouts.app')

@section('title', isset($habitacion) ? 'Editar Habitación' : 'Nueva Habitación')
@section('page-title', isset($habitacion) ? 'Editar Habitación' : 'Nueva Habitación')
@section('breadcrumb', 'Administración / Habitaciones / ' . (isset($habitacion) ? 'Editar' : 'Nueva'))

@section('content')

<style>
    .form-card {
        max-width: 680px;
        background: var(--bg, #fff);
        border: 0.5px solid var(--border, rgba(0,0,0,.08));
        border-radius: 14px;
        overflow: hidden;
    }
    .form-header {
        padding: 18px 24px;
        border-bottom: 0.5px solid var(--border, rgba(0,0,0,.08));
        display: flex; align-items: center; gap: 10px;
    }
    .form-header-icon {
        width: 36px; height: 36px; border-radius: 8px;
        background: #EEEDFE; color: #534AB7;
        display: flex; align-items: center; justify-content: center; font-size: 16px;
    }
    .form-header h2 { font-size: 15px; font-weight: 600; }
    .form-header .sub { font-size: 12px; color: var(--muted, #6b7280); }

    .form-body { padding: 24px; }

    .field-group {
        display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;
    }
    .field-group.single { grid-template-columns: 1fr; }
    .field-group.triple { grid-template-columns: 1fr 1fr 1fr; }

    .field { display: flex; flex-direction: column; gap: 5px; }
    .field label {
        font-size: 12px; font-weight: 500; color: var(--muted, #6b7280);
        text-transform: uppercase; letter-spacing: .04em;
    }
    .field label.required::after { content: ' *'; color: #dc2626; }
    .field input, .field select, .field textarea {
        height: 38px; padding: 0 12px;
        border: 0.5px solid var(--border, rgba(0,0,0,.12));
        border-radius: 8px; font-size: 13px; font-family: inherit;
        background: var(--bg, #fff); color: var(--text, #111);
        outline: none; transition: border-color .15s;
    }
    .field textarea {
        height: auto; padding: 10px 12px; resize: vertical; min-height: 90px;
    }
    .field select {
        appearance: none; cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 10px center; padding-right: 32px;
    }
    .field input:focus, .field select:focus, .field textarea:focus {
        border-color: #7F77DD; box-shadow: 0 0 0 3px rgba(127,119,221,.12);
    }
    .field .hint { font-size: 11px; color: var(--muted, #9ca3af); }
    .field .error { font-size: 11px; color: #dc2626; }

    /* ── Mantenimiento section ── */
    #mant-section {
        background: rgba(239,68,68,.04);
        border: 0.5px solid rgba(239,68,68,.25);
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 16px;
        display: none;
    }
    #mant-section.visible { display: block; }
    .mant-header {
        display: flex; align-items: center; gap: 8px;
        font-size: 13px; font-weight: 600; color: #dc2626;
        margin-bottom: 14px;
    }
    .mant-header i { font-size: 15px; }

    .section-divider {
        height: 0.5px; background: var(--border, rgba(0,0,0,.08));
        margin: 20px 0;
    }

    .form-footer {
        padding: 16px 24px;
        border-top: 0.5px solid var(--border, rgba(0,0,0,.08));
        display: flex; gap: 10px; justify-content: flex-end;
    }
    .btn-cancel {
        padding: 0 20px; height: 38px; border-radius: 8px; font-size: 13px;
        font-weight: 500; text-decoration: none; display: inline-flex; align-items: center;
        gap: 6px; border: 0.5px solid var(--border); color: var(--muted); background: transparent;
    }
    .btn-save {
        padding: 0 24px; height: 38px; border-radius: 8px; font-size: 13px;
        font-weight: 600; cursor: pointer; border: none;
        background: #534AB7; color: #fff; display: inline-flex; align-items: center; gap: 6px;
        transition: opacity .15s;
    }
    .btn-save:hover { opacity: .9; }

    /* ── Occupied warning ── */
    .ocu-warning {
        background: rgba(245,158,11,.08); border: 0.5px solid rgba(245,158,11,.3);
        border-radius: 10px; padding: 12px 16px; margin-bottom: 16px;
        display: flex; align-items: center; gap: 10px; font-size: 13px; color: #92400e;
    }
    .ocu-warning i { font-size: 18px; color: #d97706; }
</style>

<div class="form-card">
    <div class="form-header">
        <div class="form-header-icon">
            <i class="bi bi-door-open"></i>
        </div>
        <div>
            <h2>{{ isset($habitacion) ? 'Editar habitación #' . $habitacion->numero : 'Registrar nueva habitación' }}</h2>
            <div class="sub">{{ isset($habitacion) ? 'Actualiza los datos de la habitación' : 'Completa los datos para registrar una habitación' }}</div>
        </div>
    </div>

    <div class="form-body">

        {{-- Errores --}}
        @if($errors->any())
        <div style="background:rgba(239,68,68,.08);border:0.5px solid rgba(239,68,68,.25);border-radius:8px;padding:12px 14px;margin-bottom:16px">
            <ul style="margin:0;padding-left:16px;font-size:13px;color:#dc2626">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST"
              action="{{ isset($habitacion) ? route('admin.habitaciones.update', $habitacion) : route('admin.habitaciones.store') }}">
            @csrf
            @if(isset($habitacion)) @method('PUT') @endif

            {{-- Campos básicos --}}
            <div class="field-group">
                <div class="field">
                    <label class="required">Número de habitación</label>
                    <input type="text" name="numero" value="{{ old('numero', $habitacion->numero ?? '') }}"
                           placeholder="Ej: 101, A-02…" maxlength="10">
                    @error('numero') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label class="required">Piso</label>
                    <input type="number" name="piso" value="{{ old('piso', $habitacion->piso ?? '') }}"
                           min="1" placeholder="1">
                    @error('piso') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="field-group">
                <div class="field">
                    <label class="required">Tipo de habitación</label>
                    <select name="id_tipo_habitacion">
                        <option value="">Seleccionar tipo…</option>
                        @foreach($tipos as $tipo)
                        <option value="{{ $tipo->id_tipo }}"
                            {{ old('id_tipo_habitacion', $habitacion->id_tipo_habitacion ?? '') == $tipo->id_tipo ? 'selected' : '' }}>
                            {{ $tipo->nombre }} — S/ {{ number_format($tipo->precio_base, 2) }}/noche
                        </option>
                        @endforeach
                    </select>
                    @error('id_tipo_habitacion') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label class="required">Estado</label>
                    <select name="estado" id="estado-select">
                        @foreach(['disponible'=>'Disponible','no disponible'=>'No disponible (ocupada)','mantenimiento'=>'En mantenimiento'] as $val => $lbl)
                        <option value="{{ $val }}"
                            {{ old('estado', $habitacion->estado ?? 'disponible') === $val ? 'selected' : '' }}>
                            {{ $lbl }}
                        </option>
                        @endforeach
                    </select>
                    @error('estado') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- ══ SECCIÓN MANTENIMIENTO ══ --}}
            <div id="mant-section" class="{{ old('estado', $habitacion->estado ?? '') === 'mantenimiento' ? 'visible' : '' }}">

                <div class="mant-header">
                    <i class="bi bi-tools"></i>
                    Detalle del mantenimiento
                </div>

                <div class="field-group single" style="margin-bottom:12px">
                    <div class="field">
                        <label class="required">Motivo del mantenimiento</label>
                        <textarea name="motivo_mantenimiento" rows="3"
                                  placeholder="Describe el problema o trabajo a realizar. Ej: Fuga en baño, cambio de grifo y revisión de tuberías…">{{ old('motivo_mantenimiento', $habitacion->motivo_mantenimiento ?? '') }}</textarea>
                        <span class="hint">Sé específico: describe qué ocurrió y qué trabajo se realizará.</span>
                        @error('motivo_mantenimiento') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="field-group">
                    <div class="field">
                        <label>Fecha de inicio del mantenimiento</label>
                        <input type="date" name="mantenimiento_desde"
                               value="{{ old('mantenimiento_desde', isset($habitacion) ? optional($habitacion->mantenimiento_desde)->format('Y-m-d') : '') }}">
                        @error('mantenimiento_desde') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label>Fecha estimada de finalización</label>
                        <input type="date" name="mantenimiento_hasta"
                               value="{{ old('mantenimiento_hasta', isset($habitacion) ? optional($habitacion->mantenimiento_hasta)->format('Y-m-d') : '') }}">
                        <span class="hint">Fecha aproximada en que la habitación volverá a estar disponible.</span>
                        @error('mantenimiento_hasta') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>

            </div>
            {{-- ══ FIN SECCIÓN MANTENIMIENTO ══ --}}

        </form>

    </div>{{-- /form-body --}}

    <div class="form-footer">
        <a href="{{ route('admin.habitaciones.index') }}" class="btn-cancel">
            <i class="bi bi-arrow-left"></i> Cancelar
        </a>
        <button type="submit" form="hab-form" class="btn-save" onclick="this.closest('.form-body').querySelector('form').submit()">
            <i class="bi bi-check-lg"></i>
            {{ isset($habitacion) ? 'Guardar cambios' : 'Registrar habitación' }}
        </button>
    </div>

</div>

<script>
(function () {
    const estadoSel = document.getElementById('estado-select');
    const mantSec   = document.getElementById('mant-section');

    function toggle() {
        if (estadoSel.value === 'mantenimiento') {
            mantSec.classList.add('visible');
            // Marcar como requerido
            const motivo = mantSec.querySelector('textarea[name="motivo_mantenimiento"]');
            if (motivo) motivo.required = true;
        } else {
            mantSec.classList.remove('visible');
            const motivo = mantSec.querySelector('textarea[name="motivo_mantenimiento"]');
            if (motivo) motivo.required = false;
        }
    }

    estadoSel.addEventListener('change', toggle);
    toggle(); // inicializar

    // Fix: submit via botón externo al form
    const form = document.querySelector('.form-body form');
    document.querySelector('.btn-save').addEventListener('click', function(e) {
        e.preventDefault();
        form.submit();
    });
})();
</script>

@endsection