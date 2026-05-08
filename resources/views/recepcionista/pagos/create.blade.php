@extends('layouts.app')

@section('title', 'Registrar Pago')
@section('page-title', 'Registrar Pago')
@section('breadcrumb', 'Pagos / Nuevo')

@section('content')
<div style="max-width:620px">
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="bi bi-cash-coin" style="color:var(--accent2)"></i> Registrar Pago</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('recepcionista.pagos.store') }}">
            @csrf

            <div class="form-group">
                <label>Reserva</label>
                <select name="id_reserva" required id="select-reserva">
                    <option value="">Seleccionar reserva...</option>
                    @foreach($reservas as $r)
                        <option value="{{ $r->id_reserva }}"
                            data-total="{{ $r->precio_total }}"
                            data-pagado="{{ $r->pagos->sum('monto') }}"
                            {{ (old('id_reserva', $reservaSeleccionada?->id_reserva) == $r->id_reserva) ? 'selected' : '' }}>
                            #{{ $r->id_reserva }} — {{ $r->cliente->apellido }}, {{ $r->cliente->nombre }} — S/ {{ number_format($r->precio_total, 2) }}
                        </option>
                    @endforeach
                </select>
                @error('id_reserva')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Resumen saldo --}}
            <div id="saldo-preview" style="display:none; background:var(--surface2); border:1px solid var(--border); border-radius:8px; padding:14px 16px; margin-top:10px">
                <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:6px">
                    <span style="color:var(--muted)">Total reserva</span>
                    <span id="res-total" style="font-weight:500">S/ 0.00</span>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:6px">
                    <span style="color:var(--muted)">Ya pagado</span>
                    <span id="res-pagado" style="color:var(--success)">S/ 0.00</span>
                </div>
                <div style="height:1px; background:var(--border); margin:8px 0"></div>
                <div style="display:flex; justify-content:space-between; font-size:14px; font-weight:600">
                    <span>Saldo pendiente</span>
                    <span id="res-saldo" style="color:var(--warning)">S/ 0.00</span>
                </div>
            </div>

            <div class="form-grid form-grid-2 mt-4">
                <div class="form-group">
                    <label>Monto a Pagar (S/)</label>
                    <input type="number" name="monto" value="{{ old('monto') }}" step="0.01" min="0.01" required placeholder="0.00" id="input-monto">
                    @error('monto')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Método de Pago</label>
                    <select name="metodo_pago" required>
                        <option value="">Seleccionar...</option>
                        @foreach(['efectivo' => '💵 Efectivo', 'tarjeta' => '💳 Tarjeta', 'yape' => '📱 Yape', 'plin' => '📱 Plin'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('metodo_pago') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    @error('metodo_pago')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('recepcionista.pagos.index') }}" class="btn btn-ghost">Cancelar</a>
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Registrar pago</button>
            </div>
        </form>
    </div>
</div>
</div>

@push('scripts')
<script>
const sel     = document.getElementById('select-reserva');
const preview = document.getElementById('saldo-preview');
const monto   = document.getElementById('input-monto');

function actualizar() {
    const opt = sel.selectedOptions[0];
    if (!opt?.value) { preview.style.display='none'; return; }
    const total  = parseFloat(opt.dataset.total  || 0);
    const pagado = parseFloat(opt.dataset.pagado || 0);
    const saldo  = total - pagado;
    document.getElementById('res-total').textContent  = 'S/ ' + total.toFixed(2);
    document.getElementById('res-pagado').textContent = 'S/ ' + pagado.toFixed(2);
    document.getElementById('res-saldo').textContent  = 'S/ ' + saldo.toFixed(2);
    monto.max = saldo;
    preview.style.display = 'block';
}

sel.addEventListener('change', actualizar);
actualizar();
</script>
@endpush
@endsection