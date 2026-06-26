@extends('layouts.app')

@section('title', 'Habitaciones')
@section('page-title', 'Habitaciones')
@section('breadcrumb', 'Administración / Habitaciones')

@section('topbar-actions')
    <a href="{{ route('admin.habitaciones.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Nueva Habitación
    </a>
@endsection

@section('content')

<style>
    .hab-wrap { --purple-mid: #7F77DD; --purple-dark: #534AB7; --purple-bg: #EEEDFE;
                --card-bg: var(--bg,#fff); --card-border: var(--border,rgba(0,0,0,.08));
                --text-sub: var(--muted,#6b7280); }

    .hab-filter {
        display: flex; flex-wrap: wrap; align-items: flex-end; gap: 10px;
        background: var(--card-bg); border: 0.5px solid var(--card-border);
        border-radius: 12px; padding: 14px 16px; margin-bottom: 14px;
    }
    .hf-group { display: flex; flex-direction: column; gap: 4px; }
    .hf-label { font-size: 11px; font-weight: 500; color: var(--text-sub);
                text-transform: uppercase; letter-spacing: .04em; }
    .hf-input, .hf-select {
        height: 34px; padding: 0 10px; border: 0.5px solid var(--card-border);
        border-radius: 8px; background: var(--card-bg); font-size: 13px;
        outline: none; font-family: inherit;
    }
    .hf-select { padding-right: 28px; appearance: none; cursor: pointer; min-width: 150px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 8px center; }
    .hf-input:focus, .hf-select:focus { border-color: var(--purple-mid); }
    .hf-divider { width:0.5px; height:34px; background:var(--card-border); margin:0 2px; align-self:flex-end; }
    .hf-btn {
        height: 34px; padding: 0 14px; border-radius: 8px; font-size: 13px; font-weight: 500;
        cursor: pointer; border: 0.5px solid var(--card-border);
        display: inline-flex; align-items: center; gap: 6px; text-decoration: none;
        transition: opacity .15s; font-family: inherit;
    }
    .hf-btn:hover { opacity: .8; }
    .hf-apply { background: var(--purple-dark); color: #fff; border-color: var(--purple-dark); }
    .hf-reset { background: var(--card-bg); color: var(--text-sub); }

    /* ── Stat cards ── */
    .hab-stats { display: grid; grid-template-columns: repeat(4,1fr); gap: 10px; margin-bottom: 14px; }
    .hs-card {
        background: var(--card-bg); border: 0.5px solid var(--card-border);
        border-radius: 10px; padding: 12px 14px; display: flex; gap: 12px; align-items: center;
    }
    .hs-icon { width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0; }
    .hs-val   { font-size: 20px; font-weight: 700; }
    .hs-label { font-size: 11px; color: var(--text-sub); }

    /* ── Mantenimiento row highlight ── */
    tr.mant-row td { background: rgba(239,68,68,.04) !important; }
    tr.mant-row:hover td { background: rgba(239,68,68,.08) !important; }
    .mant-badge {
        display: inline-flex; align-items: center; gap: 4px;
        background: rgba(239,68,68,.12); color: #dc2626;
        font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 20px;
    }
    .mant-tooltip {
        font-size: 11px; color: #dc2626; margin-top: 3px; max-width: 220px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }

    .range-chip {
        display: inline-flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 500;
        padding: 3px 10px; border-radius: 20px; background: var(--purple-bg);
        color: var(--purple-dark); white-space: nowrap;
    }
</style>

{{-- ── FILTROS ── --}}
<form method="GET" action="{{ route('admin.habitaciones.index') }}" class="hab-wrap" id="hab-filter-form">
<div class="hab-filter">

    <div class="hf-group">
        <label class="hf-label">Estado</label>
        <select name="estado" class="hf-select">
            <option value="">Todos los estados</option>
            @foreach(['disponible'=>'Disponible','no disponible'=>'Ocupada','mantenimiento'=>'Mantenimiento'] as $val => $lbl)
            <option value="{{ $val }}" {{ ($estado??'') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
            @endforeach
        </select>
    </div>

    <div class="hf-divider"></div>

    <div class="hf-group">
        <label class="hf-label"><i class="bi bi-calendar3"></i> Uso desde</label>
        <input type="date" name="desde" class="hf-input" value="{{ $desde ?? '' }}" max="{{ date('Y-m-d') }}">
    </div>

    <div class="hf-group">
        <label class="hf-label"><i class="bi bi-calendar3"></i> Uso hasta</label>
        <input type="date" name="hasta" class="hf-input" value="{{ $hasta ?? '' }}" max="{{ date('Y-m-d') }}">
    </div>

    <div class="hf-divider"></div>

    <div class="hf-group">
        <label class="hf-label">Ordenar por</label>
        <select name="filtro" class="hf-select">
            <option value="" {{ empty($filtro) ? 'selected' : '' }}>Piso y número</option>
            <option value="mas_usadas" {{ ($filtro??'')==='mas_usadas' ? 'selected' : '' }}>
                Más usadas @if($desde||$hasta)(en rango)@endif
            </option>
        </select>
    </div>

    {{-- Accesos rápidos --}}
    <div class="hf-group">
        <label class="hf-label">Rango rápido</label>
        <div style="display:flex;gap:6px">
            @foreach([
                ['label'=>'7 días','desde'=>date('Y-m-d',strtotime('-6 days')),'hasta'=>date('Y-m-d')],
                ['label'=>'Mes',   'desde'=>date('Y-m-01'),                     'hasta'=>date('Y-m-d')],
                ['label'=>'Año',   'desde'=>date('Y-01-01'),                    'hasta'=>date('Y-m-d')],
            ] as $q)
            <a href="{{ route('admin.habitaciones.index', array_merge(request()->query(), ['desde'=>$q['desde'],'hasta'=>$q['hasta'],'filtro'=>'mas_usadas'])) }}"
               class="hf-btn hf-reset" style="font-size:12px;padding:0 10px">{{ $q['label'] }}</a>
            @endforeach
        </div>
    </div>

    <div class="hf-group" style="margin-left:auto">
        <label class="hf-label" style="visibility:hidden">.</label>
        <div style="display:flex;gap:6px">
            <button type="submit" class="hf-btn hf-apply">
                <i class="bi bi-funnel-fill" style="font-size:11px"></i> Filtrar
            </button>
            <a href="{{ route('admin.habitaciones.index') }}" class="hf-btn hf-reset">
                <i class="bi bi-x-lg" style="font-size:11px"></i> Limpiar
            </a>
        </div>
    </div>

    @if(!empty($desde) || !empty($hasta))
    <div style="width:100%;padding-top:4px">
        <div class="range-chip">
            <i class="bi bi-calendar3" style="font-size:10px"></i>
            Usos entre
            <strong>{{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }} → {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}</strong>
        </div>
    </div>
    @endif

</div>
</form>

{{-- ── STAT CARDS ── --}}
<div class="hab-stats hab-wrap">
    <div class="hs-card">
        <div class="hs-icon" style="background:rgba(83,74,183,.12);color:#534AB7"><i class="bi bi-door-open"></i></div>
        <div>
            <div class="hs-val">{{ $stats->total }}</div>
            <div class="hs-label">Total habitaciones</div>
        </div>
    </div>
    <div class="hs-card">
        <div class="hs-icon" style="background:rgba(16,185,129,.12);color:#16a34a"><i class="bi bi-check-circle"></i></div>
        <div>
            <div class="hs-val" style="color:#16a34a">{{ $stats->disponibles }}</div>
            <div class="hs-label">Disponibles</div>
        </div>
    </div>
    <div class="hs-card">
        <div class="hs-icon" style="background:rgba(245,158,11,.12);color:#d97706"><i class="bi bi-person-fill"></i></div>
        <div>
            <div class="hs-val" style="color:#d97706">{{ $stats->ocupadas }}</div>
            <div class="hs-label">Ocupadas</div>
        </div>
    </div>
    <div class="hs-card">
        <div class="hs-icon" style="background:rgba(239,68,68,.12);color:#dc2626"><i class="bi bi-tools"></i></div>
        <div>
            <div class="hs-val" style="color:#dc2626">{{ $stats->mantenimiento }}</div>
            <div class="hs-label">Mantenimiento</div>
        </div>
    </div>
</div>

{{-- ── TABLA ── --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">
            <i class="bi bi-door-open" style="color:var(--accent2)"></i>
            Lista de Habitaciones
        </span>
        <span class="badge badge-muted">{{ $habitaciones->total() }} habitaciones</span>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Piso</th>
                    <th>Tipo</th>
                    <th style="text-align:center">Capacidad</th>
                    <th style="text-align:right">Precio/noche</th>
                    <th>Estado / Mantenimiento</th>
                    <th style="text-align:center">
                        Veces usada
                        @if($desde && $hasta)
                        <div style="font-size:9px;font-weight:400;color:#9ca3af">en rango filtrado</div>
                        @endif
                    </th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($habitaciones as $h)
                <tr class="{{ $h->estado === 'mantenimiento' ? 'mant-row' : '' }}">
                    <td style="font-weight:600; font-size:15px">{{ $h->numero }}</td>
                    <td style="color:var(--muted)">{{ $h->piso }}°</td>
                    <td>{{ $h->tipo->nombre }}</td>
                    <td style="text-align:center">
                        {{ $h->tipo->capacidad }}
                        <i class="bi bi-person" style="color:var(--muted);font-size:11px"></i>
                    </td>
                    <td style="font-weight:500;text-align:right">
                        S/ {{ number_format($h->tipo->precio_base, 2) }}
                    </td>
                    <td>
                        @php $badges = ['disponible'=>'success','no disponible'=>'warning','mantenimiento'=>'danger']; @endphp
                        <span class="badge badge-{{ $badges[$h->estado] ?? 'muted' }}">
                            @if($h->estado==='mantenimiento') <i class="bi bi-tools"></i> @endif
                            {{ ucwords($h->estado) }}
                        </span>

                        {{-- Motivo mantenimiento --}}
                        @if($h->estado === 'mantenimiento' && $h->motivo_mantenimiento)
                        <div class="mant-tooltip" title="{{ $h->motivo_mantenimiento }}">
                            <i class="bi bi-info-circle"></i>
                            {{ Str::limit($h->motivo_mantenimiento, 40) }}
                        </div>
                        @if($h->mantenimiento_desde || $h->mantenimiento_hasta)
                        <div style="font-size:10px;color:#9ca3af;margin-top:2px">
                            @if($h->mantenimiento_desde)
                                Desde {{ $h->mantenimiento_desde->format('d/m/Y') }}
                            @endif
                            @if($h->mantenimiento_hasta)
                                · Hasta {{ $h->mantenimiento_hasta->format('d/m/Y') }}
                            @endif
                        </div>
                        @endif
                        @endif
                    </td>
                    <td style="text-align:center; font-weight:600; font-size:15px">
                        {{ $h->detalles_reserva_count }}
                    </td>
                    <td>
                        <div class="gap-2">
                            <a href="{{ route('admin.habitaciones.show', $h) }}"
                               class="btn btn-ghost btn-sm btn-icon" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($h->estado !== 'no disponible')
                            <a href="{{ route('admin.habitaciones.edit', $h) }}"
                               class="btn btn-ghost btn-sm btn-icon" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;color:var(--muted);padding:40px">
                        Sin habitaciones registradas
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="padding:14px 20px">{{ $habitaciones->links() }}</div>
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