@extends('layouts.app')

@section('title', 'Boletas')
@section('page-title', 'Boletas')
@section('breadcrumb', 'Inicio / Boletas')

@section('content')

<style>
    .bol-wrap { --purple-mid: #7F77DD; --purple-dark: #534AB7; --purple-bg: #EEEDFE;
                --card-bg: var(--bg,#fff); --card-border: var(--border,rgba(0,0,0,.08));
                --text-sub: var(--muted,#6b7280); }

    .bol-filter {
        display: flex; flex-wrap: wrap; align-items: flex-end; gap: 10px;
        background: var(--card-bg); border: 0.5px solid var(--card-border);
        border-radius: 12px; padding: 14px 16px; margin-bottom: 14px;
    }
    .bf-group { display: flex; flex-direction: column; gap: 4px; }
    .bf-label { font-size: 11px; font-weight: 500; color: var(--text-sub);
                text-transform: uppercase; letter-spacing: .04em; }
    .bf-input, .bf-select {
        height: 34px; padding: 0 10px; border: 0.5px solid var(--card-border);
        border-radius: 8px; background: var(--card-bg); font-size: 13px;
        outline: none; min-width: 130px; font-family: inherit;
    }
    .bf-input:focus { border-color: var(--purple-mid); }
    .bf-btn {
        height: 34px; padding: 0 14px; border-radius: 8px; font-size: 13px; font-weight: 500;
        cursor: pointer; border: 0.5px solid var(--card-border);
        display: inline-flex; align-items: center; gap: 6px; text-decoration: none;
        transition: opacity .15s; font-family: inherit;
    }
    .bf-btn:hover { opacity: .8; }
    .bf-apply  { background: var(--purple-dark); color: #fff; border-color: var(--purple-dark); }
    .bf-reset  { background: var(--card-bg); color: var(--text-sub); }
    .bf-export { background: #16a34a; color: #fff; border-color: #16a34a; }
    .bf-divider { width:0.5px; height:34px; background:var(--card-border); margin:0 2px; align-self:flex-end; }

    .total-banner {
        background: linear-gradient(135deg, #534AB7, #7F77DD);
        color: #fff; border-radius: 10px; padding: 12px 18px;
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 14px;
    }
    .total-banner .label { font-size: 12px; opacity: .85; }
    .total-banner .val   { font-size: 22px; font-weight: 700; }

    .range-chip {
        display: inline-flex; align-items: center; gap: 6px; font-size: 11px;
        font-weight: 500; padding: 3px 10px; border-radius: 20px;
        background: var(--purple-bg); color: var(--purple-dark); white-space: nowrap;
    }
    .range-chip a { color: var(--purple-dark); text-decoration: none; font-size: 13px; }
</style>

{{-- ── FILTROS ── --}}
<form method="GET" action="{{ route('recepcionista.boletas.index') }}" class="bol-wrap">
<div class="bol-filter">

    <div class="bf-group">
        <label class="bf-label"><i class="bi bi-search"></i> Buscar cliente</label>
        <input type="text" name="buscar" class="bf-input" style="min-width:180px"
               value="{{ $buscar ?? '' }}" placeholder="Nombre o documento…">
    </div>

    <div class="bf-divider"></div>

    <div class="bf-group">
        <label class="bf-label"><i class="bi bi-calendar3"></i> Desde</label>
        <input type="date" name="desde" class="bf-input" value="{{ $desde ?? '' }}" max="{{ date('Y-m-d') }}">
    </div>

    <div class="bf-group">
        <label class="bf-label"><i class="bi bi-calendar3"></i> Hasta</label>
        <input type="date" name="hasta" class="bf-input" value="{{ $hasta ?? '' }}" max="{{ date('Y-m-d') }}">
    </div>

    {{-- Accesos rápidos --}}
    <div class="bf-group">
        <label class="bf-label">Acceso rápido</label>
        <div style="display:flex;gap:6px">
            @foreach([
                ['label'=>'Hoy',    'desde'=>date('Y-m-d'),                       'hasta'=>date('Y-m-d')],
                ['label'=>'7 días', 'desde'=>date('Y-m-d',strtotime('-6 days')),  'hasta'=>date('Y-m-d')],
                ['label'=>'Mes',    'desde'=>date('Y-m-01'),                       'hasta'=>date('Y-m-d')],
            ] as $q)
            <a href="{{ route('recepcionista.boletas.index', ['desde'=>$q['desde'],'hasta'=>$q['hasta']]) }}"
               class="bf-btn bf-reset" style="font-size:12px;padding:0 10px">{{ $q['label'] }}</a>
            @endforeach
        </div>
    </div>

    <div class="bf-group" style="margin-left:auto">
        <label class="bf-label" style="visibility:hidden">.</label>
        <div style="display:flex;gap:6px">
            <button type="submit" class="bf-btn bf-apply">
                <i class="bi bi-funnel-fill" style="font-size:11px"></i> Filtrar
            </button>
            <a href="{{ route('recepcionista.boletas.index') }}" class="bf-btn bf-reset">
                <i class="bi bi-x-lg" style="font-size:11px"></i> Limpiar
            </a>
            <a href="{{ route('recepcionista.boletas.export-pdf', request()->query()) }}"
               class="bf-btn bf-export" target="_blank">
                <i class="bi bi-file-earmark-pdf" style="font-size:12px"></i> PDF
            </a>
        </div>
    </div>

    @if(!empty($desde) || !empty($hasta) || !empty($buscar))
    <div style="width:100%;display:flex;gap:8px;flex-wrap:wrap;padding-top:4px">
        @if(!empty($desde) || !empty($hasta))
        <div class="range-chip">
            <i class="bi bi-funnel-fill" style="font-size:11px"></i>
            @if($desde && $hasta)
                {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }} → {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}
            @elseif($desde)
                Desde {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }}
            @else
                Hasta {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}
            @endif
        </div>
        @endif
        @if(!empty($buscar))
        <div class="range-chip" style="background:#F0FDF4;color:#16a34a">
            <i class="bi bi-person"></i> "{{ $buscar }}"
        </div>
        @endif
    </div>
    @endif

</div>
</form>

{{-- ── BANNER TOTAL ── --}}
<div class="total-banner bol-wrap">
    <div>
        <div class="label"><i class="bi bi-receipt"></i> Total facturado en el período</div>
        <div class="val">S/ {{ number_format($totalGeneral, 2) }}</div>
    </div>
    <div style="text-align:right; font-size:12px; opacity:.85">
        <div>{{ $boletas->total() }} boletas emitidas</div>
        @if($desde || $hasta)
        <div style="margin-top:2px;opacity:.7">
            {{ $desde ? \Carbon\Carbon::parse($desde)->format('d/m/Y') : '…' }}
            →
            {{ $hasta ? \Carbon\Carbon::parse($hasta)->format('d/m/Y') : 'hoy' }}
        </div>
        @endif
    </div>
</div>

{{-- ── TABLA ── --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">
            <i class="bi bi-receipt" style="color:var(--accent2)"></i>
            Boletas Emitidas
        </span>
        <span class="badge badge-muted">{{ $boletas->total() }} registros</span>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Reserva</th>
                    <th>Cliente</th>
                    <th>Emitido por</th>
                    <th>Fecha emisión</th>
                    <th style="text-align:right">Total</th>
                    <th style="text-align:right">Acumulado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($boletas as $b)
                <tr>
                    <td class="mono" style="color:var(--muted)">{{ $b->id_boleta }}</td>
                    <td>
                        <a href="{{ route('recepcionista.reservas.show', $b->reserva) }}"
                           style="color:var(--accent2); font-weight:500">
                            #{{ $b->id_reserva }}
                        </a>
                    </td>
                    <td>
                        <div style="font-weight:500">
                            {{ $b->reserva->cliente->nombre }}
                            {{ $b->reserva->cliente->apellido }}
                        </div>
                        <div style="font-size:11px;color:var(--muted)">
                            {{ $b->reserva->cliente->documento }}
                        </div>
                    </td>
                    <td style="color:var(--muted)">{{ $b->usuario->name }}</td>
                    <td class="mono" style="font-size:12px">
                        {{ $b->fecha_emision->format('d/m/Y') }}
                        <div style="font-size:10px;color:var(--muted)">{{ $b->fecha_emision->format('H:i') }}</div>
                    </td>
                    <td style="font-weight:600; color:var(--success); text-align:right">
                        S/ {{ number_format($b->total, 2) }}
                    </td>
                    <td style="text-align:right; color:var(--muted); font-size:12px">
                        S/ {{ number_format($b->total_acumulado, 2) }}
                    </td>
                    <td>
                        <div style="display:flex;gap:4px">
                            <a href="{{ route('recepcionista.boletas.show', $b) }}"
                               class="btn btn-ghost btn-sm btn-icon" title="Ver boleta">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('recepcionista.boletas.export-boleta-pdf', $b) }}"
                               class="btn btn-ghost btn-sm btn-icon" title="Descargar PDF" target="_blank">
                                <i class="bi bi-file-earmark-pdf" style="color:#16a34a"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;color:var(--muted);padding:40px">
                        <i class="bi bi-receipt" style="font-size:32px;display:block;margin-bottom:10px;opacity:.3"></i>
                        No hay boletas que coincidan con los filtros aplicados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="padding:14px 20px">{{ $boletas->withQueryString()->links() }}</div>
</div>

<script>
(function () {
    const d = document.querySelector('input[name="desde"]');
    const h = document.querySelector('input[name="hasta"]');
    if (!d || !h) return;
    d.addEventListener('change', () => { h.min = d.value; });
    h.addEventListener('change', () => { d.max = h.value; });
    if (d.value) h.min = d.value;
    if (h.value) d.max = h.value;
})();
</script>

@endsection