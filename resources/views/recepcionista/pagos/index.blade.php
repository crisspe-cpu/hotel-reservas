@extends('layouts.app')

@section('title', 'Pagos')
@section('page-title', 'Pagos')
@section('breadcrumb', 'Inicio / Pagos')

@section('topbar-actions')
    <a href="{{ route('recepcionista.pagos.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Registrar Pago
    </a>
@endsection

@section('content')

<style>
    .pago-wrap { --purple-mid: #7F77DD; --purple-dark: #534AB7; --purple-bg: #EEEDFE;
                 --card-bg: var(--bg,#fff); --card-border: var(--border,rgba(0,0,0,.08));
                 --text-main: var(--text,#111); --text-sub: var(--muted,#6b7280); }

    /* ── Filter bar ── */
    .pago-filter {
        display: flex; flex-wrap: wrap; align-items: flex-end; gap: 10px;
        background: var(--card-bg); border: 0.5px solid var(--card-border);
        border-radius: 12px; padding: 14px 16px; margin-bottom: 14px;
    }
    .pf-group { display: flex; flex-direction: column; gap: 4px; }
    .pf-label { font-size: 11px; font-weight: 500; color: var(--text-sub);
                text-transform: uppercase; letter-spacing: .04em; }
    .pf-input, .pf-select {
        height: 34px; padding: 0 10px; border: 0.5px solid var(--card-border);
        border-radius: 8px; background: var(--card-bg); color: var(--text-main);
        font-size: 13px; outline: none; min-width: 130px; font-family: inherit;
    }
    .pf-select { padding-right: 28px; appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 8px center; cursor: pointer; }
    .pf-input:focus, .pf-select:focus { border-color: var(--purple-mid); }
    .pf-divider { width: 0.5px; height: 34px; background: var(--card-border); margin: 0 2px; align-self: flex-end; }
    .pf-btn {
        height: 34px; padding: 0 14px; border-radius: 8px; font-size: 13px;
        font-weight: 500; cursor: pointer; border: 0.5px solid var(--card-border);
        display: inline-flex; align-items: center; gap: 6px; text-decoration: none;
        transition: opacity .15s; font-family: inherit;
    }
    .pf-btn:hover { opacity: .8; }
    .pf-apply  { background: var(--purple-dark); color: #fff; border-color: var(--purple-dark); }
    .pf-reset  { background: var(--card-bg); color: var(--text-sub); }
    .pf-export { background: #16a34a; color: #fff; border-color: #16a34a; }

    /* ── Método pills ── */
    .metodo-tabs { display: flex; gap: 6px; flex-wrap: wrap; }
    .metodo-tab {
        padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500;
        border: 0.5px solid var(--card-border); cursor: pointer; text-decoration: none;
        color: var(--text-sub); background: var(--card-bg); transition: all .15s;
    }
    .metodo-tab:hover, .metodo-tab.active {
        background: var(--purple-dark); color: #fff; border-color: var(--purple-dark);
    }
    .metodo-tab.efectivo.active  { background: #0f766e; border-color: #0f766e; }
    .metodo-tab.tarjeta.active   { background: #1d4ed8; border-color: #1d4ed8; }
    .metodo-tab.yape.active      { background: #7c3aed; border-color: #7c3aed; }
    .metodo-tab.plin.active      { background: #0369a1; border-color: #0369a1; }

    /* ── Stat mini-cards ── */
    .metodo-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 8px; margin-bottom: 14px;
    }
    .ms-card {
        background: var(--card-bg); border: 0.5px solid var(--card-border);
        border-radius: 10px; padding: 10px 14px;
        display: flex; flex-direction: column; gap: 2px;
    }
    .ms-label { font-size: 11px; color: var(--text-sub); font-weight: 500; text-transform: uppercase; letter-spacing: .04em; }
    .ms-val   { font-size: 16px; font-weight: 600; color: var(--text-main); }
    .ms-icon  { font-size: 18px; margin-bottom: 4px; }

    /* ── Método badge ── */
    .m-efectivo { background: rgba(15,118,110,.12); color: #0f766e; }
    .m-tarjeta  { background: rgba(29,78,216,.12);  color: #1d4ed8; }
    .m-yape     { background: rgba(124,58,237,.12); color: #7c3aed; }
    .m-plin     { background: rgba(3,105,161,.12);  color: #0369a1; }

    /* ── Range chip ── */
    .range-chip {
        display: inline-flex; align-items: center; gap: 6px; font-size: 11px;
        font-weight: 500; padding: 3px 10px; border-radius: 20px;
        background: var(--purple-bg); color: var(--purple-dark); white-space: nowrap;
    }
    .range-chip a { color: var(--purple-dark); text-decoration: none; font-size: 13px; }
</style>

{{-- ── FILTROS ── --}}
<form method="GET" action="{{ route('recepcionista.pagos.index') }}" id="pago-filter-form">
<div class="pago-filter pago-wrap">

    {{-- Búsqueda cliente --}}
    <div class="pf-group">
        <label class="pf-label"><i class="bi bi-search"></i> Buscar</label>
        <input type="text" name="buscar" class="pf-input" style="min-width:180px"
               value="{{ $buscar ?? '' }}" placeholder="Nombre o documento…">
    </div>

    <div class="pf-divider"></div>

    {{-- Desde --}}
    <div class="pf-group">
        <label class="pf-label"><i class="bi bi-calendar3"></i> Desde</label>
        <input type="date" name="desde" class="pf-input" value="{{ $desde ?? '' }}" max="{{ date('Y-m-d') }}">
    </div>

    {{-- Hasta --}}
    <div class="pf-group">
        <label class="pf-label"><i class="bi bi-calendar3"></i> Hasta</label>
        <input type="date" name="hasta" class="pf-input" value="{{ $hasta ?? '' }}" max="{{ date('Y-m-d') }}">
    </div>

    <div class="pf-divider"></div>

    {{-- Método de pago --}}
    <div class="pf-group">
        <label class="pf-label">Método de pago</label>
        <select name="metodo_pago" class="pf-select">
            <option value="">Todos</option>
            @foreach(['efectivo','tarjeta','yape','plin'] as $m)
            <option value="{{ $m }}" {{ ($metodo ?? '') === $m ? 'selected' : '' }}>
                {{ ucfirst($m) }}
            </option>
            @endforeach
        </select>
    </div>

    {{-- Accesos rápidos --}}
    <div class="pf-group">
        <label class="pf-label">Acceso rápido</label>
        <div style="display:flex;gap:6px">
            @foreach([
                ['label'=>'Hoy',    'desde'=>date('Y-m-d'),               'hasta'=>date('Y-m-d')],
                ['label'=>'7 días', 'desde'=>date('Y-m-d',strtotime('-6 days')), 'hasta'=>date('Y-m-d')],
                ['label'=>'Mes',    'desde'=>date('Y-m-01'),               'hasta'=>date('Y-m-d')],
            ] as $q)
            <a href="{{ route('recepcionista.pagos.index', ['desde'=>$q['desde'],'hasta'=>$q['hasta'],'metodo_pago'=>$metodo??'']) }}"
               class="pf-btn pf-reset" style="font-size:12px;padding:0 10px">{{ $q['label'] }}</a>
            @endforeach
        </div>
    </div>

    {{-- Botones --}}
    <div class="pf-group" style="margin-left:auto">
        <label class="pf-label" style="visibility:hidden">.</label>
        <div style="display:flex;gap:6px">
            <button type="submit" class="pf-btn pf-apply">
                <i class="bi bi-funnel-fill" style="font-size:11px"></i> Filtrar
            </button>
            <a href="{{ route('recepcionista.pagos.index') }}" class="pf-btn pf-reset">
                <i class="bi bi-x-lg" style="font-size:11px"></i> Limpiar
            </a>
            <a href="{{ route('recepcionista.pagos.export-pdf', request()->query()) }}"
               class="pf-btn pf-export" target="_blank">
                <i class="bi bi-file-earmark-pdf" style="font-size:12px"></i> PDF
            </a>
        </div>
    </div>

    {{-- Chip rango activo --}}
    @if(!empty($desde) || !empty($hasta) || !empty($metodo) || !empty($buscar))
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
            <a href="{{ route('recepcionista.pagos.index', array_merge(request()->except(['desde','hasta']), [])) }}">×</a>
        </div>
        @endif
        @if(!empty($metodo))
        <div class="range-chip" style="background:#EDE9FE;color:#7c3aed">
            <i class="bi bi-credit-card"></i> {{ ucfirst($metodo) }}
            <a href="{{ route('recepcionista.pagos.index', array_merge(request()->except('metodo_pago'), [])) }}" style="color:#7c3aed">×</a>
        </div>
        @endif
        @if(!empty($buscar))
        <div class="range-chip" style="background:#F0FDF4;color:#16a34a">
            <i class="bi bi-person"></i> "{{ $buscar }}"
            <a href="{{ route('recepcionista.pagos.index', array_merge(request()->except('buscar'), [])) }}" style="color:#16a34a">×</a>
        </div>
        @endif
    </div>
    @endif

</div>
</form>

{{-- ── STATS POR MÉTODO ── --}}
<div class="metodo-stats pago-wrap">
    @php
        $metodosConfig = [
            'efectivo' => ['icon'=>'bi-cash-coin',    'label'=>'Efectivo',  'color'=>'#0f766e'],
            'tarjeta'  => ['icon'=>'bi-credit-card',  'label'=>'Tarjeta',   'color'=>'#1d4ed8'],
            'yape'     => ['icon'=>'bi-phone-fill',   'label'=>'Yape',      'color'=>'#7c3aed'],
            'plin'     => ['icon'=>'bi-phone',        'label'=>'Plin',      'color'=>'#0369a1'],
        ];
    @endphp

    {{-- Total general --}}
    <div class="ms-card" style="border-color: rgba(83,74,183,.25)">
        <div class="ms-icon" style="color:#534AB7"><i class="bi bi-cash-stack"></i></div>
        <div class="ms-label">Total general</div>
        <div class="ms-val" style="color:#534AB7">S/ {{ number_format($totalGeneral, 2) }}</div>
    </div>

    @foreach($metodosConfig as $key => $cfg)
    @php $monto = $totalesPorMetodo[$key] ?? 0; @endphp
    <div class="ms-card">
        <div class="ms-icon" style="color:{{ $cfg['color'] }}"><i class="bi {{ $cfg['icon'] }}"></i></div>
        <div class="ms-label">{{ $cfg['label'] }}</div>
        <div class="ms-val" style="color:{{ $cfg['color'] }}">S/ {{ number_format($monto, 2) }}</div>
    </div>
    @endforeach
</div>

{{-- ── TABS RÁPIDOS POR MÉTODO ── --}}
<div class="metodo-tabs pago-wrap" style="margin-bottom:12px">
    <a href="{{ route('recepcionista.pagos.index', array_merge(request()->except('metodo_pago'), ['metodo_pago'=>''])) }}"
       class="metodo-tab {{ empty($metodo) ? 'active' : '' }}">
        <i class="bi bi-grid-3x3-gap"></i> Todos
    </a>
    @foreach(['efectivo','tarjeta','yape','plin'] as $m)
    <a href="{{ route('recepcionista.pagos.index', array_merge(request()->query(), ['metodo_pago'=>$m])) }}"
       class="metodo-tab {{ $m }} {{ ($metodo??'') === $m ? 'active' : '' }}">
        @if($m==='efectivo') <i class="bi bi-cash-coin"></i>
        @elseif($m==='tarjeta') <i class="bi bi-credit-card"></i>
        @elseif($m==='yape') <i class="bi bi-phone-fill"></i>
        @else <i class="bi bi-phone"></i>
        @endif
        {{ ucfirst($m) }}
    </a>
    @endforeach
</div>

{{-- ── TABLA ── --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">
            <i class="bi bi-credit-card" style="color:var(--accent2)"></i>
            Historial de Pagos
        </span>
        <span class="badge badge-muted">{{ $pagos->total() }} registros</span>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Reserva</th>
                    <th>Cliente</th>
                    <th>Fecha y hora</th>
                    <th>Método</th>
                    <th style="text-align:right">Monto</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($pagos as $p)
                <tr>
                    <td class="mono" style="color:var(--muted)">{{ $p->id_pago }}</td>
                    <td>
                        <a href="{{ route('recepcionista.reservas.show', $p->reserva) }}"
                           style="color:var(--accent2); font-weight:500">
                            #{{ $p->id_reserva }}
                        </a>
                    </td>
                    <td>
                        <div style="font-weight:500">
                            {{ $p->reserva->cliente->nombre }}
                            {{ $p->reserva->cliente->apellido }}
                        </div>
                        <div style="font-size:11px;color:var(--muted)">
                            {{ $p->reserva->cliente->documento }}
                        </div>
                    </td>
                    <td class="mono" style="font-size:12px;color:var(--muted)">
                        {{ $p->fecha_pago->format('d/m/Y') }}
                        <div style="font-size:10px">{{ $p->fecha_pago->format('H:i') }}</div>
                    </td>
                    <td>
                        <span class="badge m-{{ $p->metodo_pago }}">
                            @if($p->metodo_pago==='efectivo') <i class="bi bi-cash-coin"></i>
                            @elseif($p->metodo_pago==='tarjeta') <i class="bi bi-credit-card"></i>
                            @elseif($p->metodo_pago==='yape') <i class="bi bi-phone-fill"></i>
                            @else <i class="bi bi-phone"></i>
                            @endif
                            {{ ucfirst($p->metodo_pago) }}
                        </span>
                    </td>
                    <td style="font-weight:600; text-align:right; font-size:14px">
                        S/ {{ number_format($p->monto, 2) }}
                    </td>
                    
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;color:var(--muted);padding:40px">
                        <i class="bi bi-credit-card" style="font-size:32px;display:block;margin-bottom:10px;opacity:.3"></i>
                        No hay pagos que coincidan con los filtros aplicados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="padding:14px 20px">{{ $pagos->withQueryString()->links() }}</div>
</div>

<script>
(function () {
    const d = document.querySelector('input[name="desde"]');
    const h = document.querySelector('input[name="hasta"]');
    if (!d || !h) return;
    d.addEventListener('change', () => { h.min = d.value; if (h.value && h.value < d.value) h.value = d.value; });
    h.addEventListener('change', () => { d.max = h.value; if (d.value && d.value > h.value) d.value = h.value; });
    if (d.value) h.min = d.value;
    if (h.value) d.max = h.value;
})();
</script>

@endsection