@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Inicio / Administración')

@section('content')

{{-- ════════════════════════════════════════════
    ESTILOS DEL DASHBOARD
════════════════════════════════════════════ --}}
<style>
    /* ── Fuente ── */
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap');

    .dash-wrap * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
    .dash-wrap .mono { font-family: 'DM Mono', monospace; }

    /* ── Tokens de color ── */
    .dash-wrap {
        --purple-bg:   #EEEDFE;
        --purple-mid:  #7F77DD;
        --purple-dark: #534AB7;
        --teal-bg:     #E1F5EE;
        --teal-dark:   #0F6E56;
        --amber-bg:    #FAEEDA;
        --amber-dark:  #854F0B;
        --green-bg:    #EAF3DE;
        --green-dark:  #3B6D11;
        --card-bg:     var(--bg, #fff);
        --card-border: var(--border, rgba(0,0,0,.08));
        --text-main:   var(--text, #111);
        --text-sub:    var(--muted, #6b7280);
    }

    /* ── Barra de filtros ── */
    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 10px;
        background: var(--card-bg);
        border: 0.5px solid var(--card-border);
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 14px;
    }
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .filter-group label {
        font-size: 11px;
        font-weight: 500;
        color: var(--text-sub);
        text-transform: uppercase;
        letter-spacing: .04em;
    }
    .filter-input {
        height: 34px;
        padding: 0 10px;
        border: 0.5px solid var(--card-border);
        border-radius: 8px;
        background: var(--card-bg);
        color: var(--text-main);
        font-size: 13px;
        font-family: 'DM Sans', sans-serif;
        outline: none;
        transition: border-color .15s;
        min-width: 130px;
    }
    .filter-input:focus { border-color: var(--purple-mid); }
    .filter-select {
        height: 34px;
        padding: 0 28px 0 10px;
        border: 0.5px solid var(--card-border);
        border-radius: 8px;
        background: var(--card-bg);
        color: var(--text-main);
        font-size: 13px;
        font-family: 'DM Sans', sans-serif;
        outline: none;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 8px center;
        cursor: pointer;
        min-width: 130px;
    }
    .filter-select:focus { border-color: var(--purple-mid); }
    .filter-divider {
        width: 0.5px;
        height: 34px;
        background: var(--card-border);
        align-self: flex-end;
        margin: 0 2px;
    }
    .btn-filter {
        height: 34px;
        padding: 0 16px;
        border-radius: 8px;
        font-size: 13px;
        font-family: 'DM Sans', sans-serif;
        font-weight: 500;
        cursor: pointer;
        border: 0.5px solid var(--card-border);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: opacity .15s;
    }
    .btn-filter:hover { opacity: .8; }
    .btn-apply  { background: var(--purple-dark); color: #fff; border-color: var(--purple-dark); }
    .btn-reset  { background: var(--card-bg);     color: var(--text-sub); }

    /* ── Chip de rango activo ── */
    .range-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 500;
        padding: 3px 10px;
        border-radius: 20px;
        background: var(--purple-bg);
        color: var(--purple-dark);
        margin-left: auto;
        align-self: flex-end;
        white-space: nowrap;
    }
    .range-chip a {
        color: var(--purple-dark);
        text-decoration: none;
        font-size: 13px;
        line-height: 1;
    }

    /* ── Stat cards ── */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(155px, 1fr));
        gap: 10px;
        margin-bottom: 14px;
    }
    .stat-card-new {
        background: var(--card-bg);
        border: 0.5px solid var(--card-border);
        border-radius: 12px;
        padding: 1rem 1.1rem;
        display: flex;
        flex-direction: column;
        gap: 6px;
        transition: box-shadow .2s;
    }
    .stat-card-new:hover { box-shadow: 0 2px 12px rgba(0,0,0,.06); }
    .stat-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 4px;
    }
    .stat-icon-new {
        width: 36px; height: 36px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 17px;
    }
    .stat-pill {
        font-size: 11px; font-weight: 500;
        padding: 2px 8px; border-radius: 20px;
    }
    .stat-val-new {
        font-size: 22px; font-weight: 600;
        color: var(--text-main); line-height: 1.1;
    }
    .stat-val-new.sm { font-size: 17px; }
    .stat-label-new { font-size: 12px; color: var(--text-sub); }
    .stat-sub-val { font-size: 13px; color: var(--text-sub); font-weight: 400; }

    /* ── Main grid ── */
    .main-grid {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(0, 1fr);
        gap: 12px;
    }

    /* ── Cards ── */
    .panel {
        background: var(--card-bg);
        border: 0.5px solid var(--card-border);
        border-radius: 12px;
        overflow: hidden;
    }
    .panel-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 16px;
        border-bottom: 0.5px solid var(--card-border);
    }
    .panel-title {
        font-size: 13px; font-weight: 500;
        color: var(--text-main);
        display: flex; align-items: center; gap: 7px;
    }
    .panel-title i { font-size: 15px; }
    .btn-ghost-sm {
        font-size: 12px; color: var(--text-sub);
        background: none;
        border: 0.5px solid var(--card-border);
        padding: 4px 10px; border-radius: 6px;
        cursor: pointer; text-decoration: none;
        transition: background .15s;
    }
    .btn-ghost-sm:hover { background: rgba(0,0,0,.04); }

    /* ── Tabla ── */
    .tbl-wrap { overflow-x: auto; }
    .tbl {
        width: 100%; border-collapse: collapse;
    }
    .tbl thead th {
        font-size: 11px; color: var(--text-sub); font-weight: 500;
        text-align: left; padding: 9px 14px;
        border-bottom: 0.5px solid var(--card-border);
        white-space: nowrap;
        text-transform: uppercase; letter-spacing: .03em;
    }
    .tbl thead th.r { text-align: right; }
    .tbl tbody td {
        font-size: 13px; padding: 10px 14px;
        border-bottom: 0.5px solid var(--card-border);
        color: var(--text-main);
    }
    .tbl tbody tr:last-child td { border-bottom: none; }
    .tbl tbody tr:hover td { background: rgba(0,0,0,.02); }
    .tbl td.r { text-align: right; }

    /* ── Pill de estado ── */
    .spill {
        display: inline-block;
        font-size: 11px; font-weight: 500;
        padding: 3px 9px; border-radius: 20px;
    }
    .spill-success { background: rgba(16,185,129,.12); color: #065f46; }
    .spill-warning { background: rgba(245,158,11,.12);  color: #78350f; }
    .spill-danger  { background: rgba(239,68,68,.12);   color: #7f1d1d; }
    .spill-info    { background: rgba(56,189,248,.12);  color: #0369a1; }
    .spill-muted   { background: rgba(0,0,0,.06);       color: var(--text-sub); }

    /* ── Ocupación ── */
    .ocu-body { padding: 14px 16px; }
    .ocu-row { margin-bottom: 14px; }
    .ocu-meta {
        display: flex; justify-content: space-between;
        font-size: 13px; margin-bottom: 5px;
    }
    .ocu-val { font-weight: 500; color: var(--text-sub); }
    .track {
        height: 5px; background: rgba(0,0,0,.07);
        border-radius: 4px; overflow: hidden;
    }
    .fill {
        height: 100%; border-radius: 4px;
        transition: width .5s cubic-bezier(.4,0,.2,1);
    }
    .ocu-divider {
        border: none;
        border-top: 0.5px solid var(--card-border);
        margin: 12px 0;
    }
    .ocu-footer {
        display: flex; justify-content: space-between;
        align-items: center; font-size: 12px;
        color: var(--text-sub);
    }
    .ocu-count { font-size: 14px; font-weight: 600; }

    /* ── Empty state ── */
    .empty-state {
        text-align: center; color: var(--text-sub);
        padding: 32px 0; font-size: 13px;
    }

    /* ── Resumen de filtro aplicado ── */
    .filter-summary {
        font-size: 12px; color: var(--text-sub);
        padding: 8px 16px;
        border-bottom: 0.5px solid var(--card-border);
        background: rgba(127,119,221,.04);
        display: flex; align-items: center; gap: 6px;
    }
    .filter-summary i { font-size: 13px; color: var(--purple-mid); }
</style>

<div class="dash-wrap">

{{-- ════════════════════════════════════════════
    BARRA DE FILTROS
════════════════════════════════════════════ --}}
<form method="GET" action="{{ route('admin.dashboard') }}" id="filter-form">
<div class="filter-bar">

    {{-- Desde --}}
    <div class="filter-group">
        <label for="desde"><i class="bi bi-calendar3"></i> Desde</label>
        <input
            type="date"
            id="desde"
            name="desde"
            class="filter-input"
            value="{{ $desde ?? '' }}"
            max="{{ date('Y-m-d') }}"
        >
    </div>

    {{-- Hasta --}}
    <div class="filter-group">
        <label for="hasta"><i class="bi bi-calendar3"></i> Hasta</label>
        <input
            type="date"
            id="hasta"
            name="hasta"
            class="filter-input"
            value="{{ $hasta ?? '' }}"
            max="{{ date('Y-m-d') }}"
        >
    </div>

    <div class="filter-divider"></div>

    {{-- Estado --}}
    <div class="filter-group">
        <label>Estado reserva</label>
        <select name="estado" class="filter-select">
            <option value="">Todos</option>
            @foreach(['confirmada','pendiente','cancelada','finalizada'] as $est)
            <option value="{{ $est }}" {{ ($estadoFiltro ?? '') === $est ? 'selected' : '' }}>
                {{ ucfirst($est) }}
            </option>
            @endforeach
        </select>
    </div>

    {{-- Accesos rápidos --}}
    <div class="filter-group">
        <label>Acceso rápido</label>
        <div style="display:flex;gap:6px">
            @foreach([
                ['label'=>'Hoy',      'desde'=> date('Y-m-d'),                      'hasta'=> date('Y-m-d')],
                ['label'=>'7 días',   'desde'=> date('Y-m-d', strtotime('-6 days')), 'hasta'=> date('Y-m-d')],
                ['label'=>'Este mes', 'desde'=> date('Y-m-01'),                      'hasta'=> date('Y-m-d')],
            ] as $quick)
            <a
                href="{{ route('admin.dashboard', ['desde'=>$quick['desde'],'hasta'=>$quick['hasta'],'estado'=>$estadoFiltro??'']) }}"
                class="btn-filter btn-reset"
                style="height:34px;font-size:12px;padding:0 10px"
            >{{ $quick['label'] }}</a>
            @endforeach
        </div>
    </div>

    {{-- Botones --}}
    <div class="filter-group" style="margin-left:auto">
        <label style="visibility:hidden">.</label>
        <div style="display:flex;gap:6px">
            <button type="submit" class="btn-filter btn-apply">
                <i class="bi bi-funnel-fill" style="font-size:12px"></i>
                Filtrar
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn-filter btn-reset">
                <i class="bi bi-x-lg" style="font-size:11px"></i>
                Limpiar
            </a>
        </div>
    </div>

    {{-- Chip de rango activo --}}
    @if(!empty($desde) || !empty($hasta))
    <div class="range-chip" style="margin-left:0;width:100%">
        <i class="bi bi-funnel-fill" style="font-size:11px"></i>
        Mostrando:
        <strong>
            @if(!empty($desde) && !empty($hasta))
                {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }}
                →
                {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}
            @elseif(!empty($desde))
                Desde {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }}
            @else
                Hasta {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}
            @endif
            @if(!empty($estadoFiltro)) · {{ ucfirst($estadoFiltro) }} @endif
        </strong>
        <a href="{{ route('admin.dashboard') }}" title="Quitar filtro">×</a>
    </div>
    @endif

</div>
</form>

{{-- ════════════════════════════════════════════
    STAT CARDS
════════════════════════════════════════════ --}}
<div class="stat-grid">

    {{-- Reservas en rango --}}
    <div class="stat-card-new">
        <div class="stat-top">
            <div class="stat-icon-new" style="background:var(--purple-bg);color:var(--purple-dark)">
                <i class="bi bi-calendar-check"></i>
            </div>
            <span class="stat-pill" style="background:rgba(56,189,248,.12);color:#0369a1">
                {{ (!empty($desde)||!empty($hasta)) ? 'Rango' : 'Hoy' }}
            </span>
        </div>
        <div class="stat-val-new">{{ $totalReservasHoy }}</div>
        <div class="stat-label-new">Reservas registradas</div>
    </div>

    {{-- Habitaciones --}}
    <div class="stat-card-new">
        <div class="stat-top">
            <div class="stat-icon-new" style="background:var(--teal-bg);color:var(--teal-dark)">
                <i class="bi bi-door-open"></i>
            </div>
            <span class="stat-pill" style="background:rgba(16,185,129,.12);color:#065f46">Libre</span>
        </div>
        <div class="stat-val-new">
            {{ $habitacionesDisponibles }}<span class="stat-sub-val"> / {{ $totalHabitaciones }}</span>
        </div>
        <div class="stat-label-new">Habitaciones disponibles</div>
    </div>

    {{-- Clientes --}}
    <div class="stat-card-new">
        <div class="stat-top">
            <div class="stat-icon-new" style="background:var(--amber-bg);color:var(--amber-dark)">
                <i class="bi bi-people"></i>
            </div>
        </div>
        <div class="stat-val-new">{{ $totalClientes }}</div>
        <div class="stat-label-new">Clientes registrados</div>
    </div>

    {{-- Ingresos --}}
    <div class="stat-card-new">
        <div class="stat-top">
            <div class="stat-icon-new" style="background:var(--green-bg);color:var(--green-dark)">
                <i class="bi bi-cash-coin"></i>
            </div>
            <span class="stat-pill" style="background:rgba(16,185,129,.12);color:#065f46">
                {{ (!empty($desde)||!empty($hasta)) ? 'Rango' : now()->translatedFormat('M') }}
            </span>
        </div>
        <div class="stat-val-new sm">S/ {{ number_format($ingresosMes, 2) }}</div>
        <div class="stat-label-new">
            @if(!empty($desde) || !empty($hasta))
                Ingresos en el períodoo
            @else
                Ingresos {{ now()->translatedFormat('F Y') }}
            @endif
        </div>
    </div>

</div>

{{-- ════════════════════════════════════════════
    GRID PRINCIPAL
════════════════════════════════════════════ --}}
<div class="main-grid">

    <div class="panel">

    <div class="panel-head">
        <span class="panel-title">
            <i class="bi bi-door-open" style="color:var(--purple-mid)"></i>
            Habitaciones más usadas
        </span>
    </div>

    <div class="tbl-wrap">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Habitación</th>
                    <th>Estado</th>
                    <th class="r">Veces usada</th>
                </tr>
            </thead>

            <tbody>
                @php $rank = 1; @endphp

                    @forelse($habitacionesMasUsadas as $h)
                    <tr>
                        <td class="mono">#{{ $rank++ }} Hab. {{ $h->numero }}</td>

                        <td>
                            <span class="spill spill-muted">
                                {{ ucfirst($h->estado) }}
                            </span>
                        </td>

                        <td class="r mono" style="font-weight:600">
                            {{ $h->total_usos }}
                        </td>
                    </tr>
                    @empty
                <tr>
                    <td colspan="3" class="empty-state">
                        <i class="bi bi-door-closed" style="font-size:24px;display:block;margin-bottom:8px;opacity:.35"></i>
                        No hay datos para el filtro seleccionado
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

    {{-- ── Ocupación por tipo ── --}}
    <div class="panel">

        <div class="panel-head">
            <span class="panel-title">
                <i class="bi bi-bar-chart-line" style="color:var(--purple-mid)"></i>
                Ocupación por tipo
            </span>
        </div>

        <div class="ocu-body">

            @forelse($ocupacionPorTipo as $tipo)
            @php $pct = min(($tipo->total / max($ocupacionPorTipo->max('total'), 1)) * 100, 100); @endphp
            <div class="ocu-row">
                <div class="ocu-meta">
                    <span>{{ $tipo->nombre }}</span>
                    <span class="ocu-val">{{ $tipo->total }}</span>
                </div>
                <div class="track">
                    <div class="fill" style="width:{{ $pct }}%;background:var(--purple-mid)"></div>
                </div>
            </div>
            @empty
            <p class="empty-state">Sin ocupación registrada</p>
            @endforelse

            <hr class="ocu-divider">

            <div class="ocu-footer">
                <span>Habitaciones ocupadas</span>
                <span class="ocu-count" style="color:var(--amber-dark)">
                    {{ $habitacionesOcupadas }} / {{ $totalHabitaciones }}
                </span>
            </div>

            @php
                $pctOcu   = $totalHabitaciones > 0 ? round(($habitacionesOcupadas / $totalHabitaciones) * 100) : 0;
                $barColor = $pctOcu > 80 ? '#D85A30' : ($pctOcu > 50 ? '#EF9F27' : '#1D9E75');
            @endphp

            <div class="track" style="height:8px;margin-top:10px">
                <div class="fill" style="width:{{ $pctOcu }}%;background:{{ $barColor }}"></div>
            </div>
            <div style="text-align:right;font-size:11px;color:var(--text-sub);margin-top:4px">
                {{ $pctOcu }}% ocupación total
            </div>

        </div>
    </div>

</div>{{-- /main-grid --}}

</div>{{-- /dash-wrap --}}

{{-- ════════════════════════════════════════════
    JS: validación rango de fechas
════════════════════════════════════════════ --}}
<script>
(function () {
    const desde = document.getElementById('desde');
    const hasta = document.getElementById('hasta');

    if (!desde || !hasta) return;

    desde.addEventListener('change', function () {
        hasta.min = this.value;
        if (hasta.value && hasta.value < this.value) hasta.value = this.value;
    });

    hasta.addEventListener('change', function () {
        desde.max = this.value;
        if (desde.value && desde.value > this.value) desde.value = this.value;
    });

    // Inicializar con valores actuales
    if (desde.value) hasta.min = desde.value;
    if (hasta.value) desde.max = hasta.value;
})();
</script>

@endsection