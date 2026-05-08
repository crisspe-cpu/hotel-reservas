@extends('layouts.app')

@section('title', isset($reserva) ? 'Editar Reserva' : 'Nueva Reserva')
@section('page-title', isset($reserva) ? 'Editar Reserva' : 'Nueva Reserva')
@section('breadcrumb', 'Reservas / ' . (isset($reserva) ? 'Editar' : 'Nueva'))

@section('content')
<div style="max-width:720px">
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="bi bi-calendar-plus" style="color:var(--accent2)"></i>
            {{ isset($reserva) ? 'Editar Reserva #'.$reserva->id_reserva : 'Registrar nueva reserva' }}
        </span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ isset($reserva) ? route('recepcionista.reservas.update', $reserva) : route('recepcionista.reservas.store') }}" id="form-reserva">
            @csrf
            @if(isset($reserva)) @method('PUT') @endif

            <div class="form-group">
                <label>Cliente</label>
                <select name="id_cliente" required>
                    <option value="">Seleccionar cliente...</option>
                    @foreach($clientes as $c)
                        <option value="{{ $c->id_cliente }}"
                            {{ old('id_cliente', $reserva->id_cliente ?? request('id_cliente')) == $c->id_cliente ? 'selected' : '' }}>
                            {{ $c->apellido }}, {{ $c->nombre }} — {{ $c->documento }}
                        </option>
                    @endforeach
                </select>
                @error('id_cliente')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-grid form-grid-2 mt-4">
                <div class="form-group">
                    <label>Fecha de Entrada</label>
                    <input type="date" name="fecha_entrada" value="{{ old('fecha_entrada', isset($reserva) ? $reserva->fecha_entrada->format('Y-m-d') : '') }}" required min="{{ date('Y-m-d') }}">
                    @error('fecha_entrada')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Fecha de Salida</label>
                    <input type="date" name="fecha_salida" value="{{ old('fecha_salida', isset($reserva) ? $reserva->fecha_salida->format('Y-m-d') : '') }}" required id="fecha-salida">
                    @error('fecha_salida')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-grid form-grid-2 mt-4">
                <div class="form-group">
                    <label>N° Huéspedes</label>
                    <input type="number" name="num_huespedes" value="{{ old('num_huespedes', $reserva->num_huespedes ?? 1) }}" min="1" max="10" required>
                    @error('num_huespedes')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                @if(isset($reserva))
                <div class="form-group">
                    <label>Estado</label>
                    <select name="estado" required>
                        @foreach(['pendiente','confirmada','cancelada'] as $e)
                            <option value="{{ $e }}" {{ $reserva->estado == $e ? 'selected' : '' }}>{{ ucfirst($e) }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>

            @if(!isset($reserva))
            <div class="form-group mt-4">
                <label>Habitación</label>
                <select name="id_habitacion" required id="select-habitacion">
                    <option value="">Seleccionar habitación...</option>
                    @foreach($habitaciones as $h)
                        <option value="{{ $h->id_habitacion }}"
                            data-precio="{{ $h->tipo->precio_base }}"
                            data-capacidad="{{ $h->tipo->capacidad }}"
                            {{ old('id_habitacion') == $h->id_habitacion ? 'selected' : '' }}>
                            Hab. {{ $h->numero }} — {{ $h->tipo->nombre }} — S/ {{ number_format($h->tipo->precio_base, 2) }}/noche — Cap. {{ $h->tipo->capacidad }}
                        </option>
                    @endforeach
                </select>
                @error('id_habitacion')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Precio estimado --}}
            <div id="precio-preview" style="display:none; background:var(--surface2); border:1px solid var(--border); border-radius:8px; padding:14px 16px; margin-top:12px">
                <div style="font-size:12px; color:var(--muted); margin-bottom:4px">PRECIO ESTIMADO</div>
                <div style="font-size:20px; font-weight:600; color:var(--accent2)" id="precio-total">S/ 0.00</div>
                <div style="font-size:12px; color:var(--muted)" id="precio-detalle"></div>
            </div>
            @endif

            <div class="form-group mt-4">
                <label>Notas (opcional)</label>
                <textarea name="notas" placeholder="Observaciones sobre la reserva...">{{ old('notas', $reserva->notas ?? '') }}</textarea>
            </div>

            <div class="form-actions">
                <a href="{{ route('recepcionista.reservas.index') }}" class="btn btn-ghost">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i>
                    {{ isset($reserva) ? 'Guardar cambios' : 'Crear reserva' }}
                </button>
            </div>
        </form>
    </div>
</div>
</div>

@push('scripts')
<script>
    const entrada   = document.querySelector('[name=fecha_entrada]');
    const salida    = document.querySelector('[name=fecha_salida]');
    const selectHab = document.getElementById('select-habitacion');
    const preview   = document.getElementById('precio-preview');
    const total     = document.getElementById('precio-total');
    const detalle   = document.getElementById('precio-detalle');

    function calcular() {
        if (!selectHab) return;
        const opt    = selectHab.selectedOptions[0];
        const precio = parseFloat(opt?.dataset?.precio || 0);
        if (!entrada?.value || !salida?.value || !precio) { preview.style.display='none'; return; }
        const noches = Math.ceil((new Date(salida.value) - new Date(entrada.value)) / 86400000);
        if (noches <= 0) { preview.style.display='none'; return; }
        preview.style.display = 'block';
        total.textContent   = 'S/ ' + (precio * noches).toFixed(2);
        detalle.textContent = `${noches} noche(s) × S/ ${precio.toFixed(2)}`;
    }

    entrada?.addEventListener('change', () => { if (salida) salida.min = entrada.value; calcular(); });
    salida?.addEventListener('change', calcular);
    selectHab?.addEventListener('change', calcular);
</script>
@endpush
@endsection