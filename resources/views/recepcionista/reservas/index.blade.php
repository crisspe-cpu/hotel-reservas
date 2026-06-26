@extends('layouts.app')

@section('title', 'Reservas')
@section('page-title', 'Reservas')
@section('breadcrumb', 'Inicio / Reservas')

@section('topbar-actions')
    <a href="{{ route('recepcionista.reservas.create') }}" class="btn btn-primary">
        <i class="bi bi-calendar-plus"></i> Nueva Reserva
    </a>
@endsection

@section('content')

<style>
    .res-wrap { --purple-mid: #7F77DD; --purple-dark: #534AB7; --purple-bg: #EEEDFE;
                --card-bg: var(--bg,#fff); --card-border: var(--border,rgba(0,0,0,.08));
                --text-sub: var(--muted,#6b7280); }

    /* ── Filter bar ── */
    .res-filter {
        display: flex; flex-wrap: wrap; align-items: flex-end; gap: 10px;
        background: var(--card-bg); border: 0.5px solid var(--card-border);
        border-radius: 12px; padding: 14px 16px; margin-bottom: 14px;
    }
    .rf-group { display: flex; flex-direction: column; gap: 4px; }
    .rf-label { font-size: 11px; font-weight: 500; color: var(--text-sub);
                text-transform: uppercase; letter-spacing: .04em; }
    .rf-input, .rf-select {
        height: 34px; padding: 0 10px; border: 0.5px solid var(--card-border);
        border-radius: 8px; background: var(--card-bg); font-size: 13px;
        outline: none; min-width: 130px; font-family: inherit;
    }
    .rf-select { padding-right: 28px; appearance: none; cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 8px center; }
    .rf-input:focus, .rf-select:focus { border-color: var(--purple-mid); }
    .rf-divider { width:0.5px; height:34px; background:var(--card-border); margin:0 2px; align-self:flex-end; }
    .rf-btn {
        height: 34px; padding: 0 14px; border-radius: 8px; font-size: 13px; font-weight: 500;
        cursor: pointer; border: 0.5px solid var(--card-border);
        display: inline-flex; align-items: center; gap: 6px; text-decoration: none;
        transition: opacity .15s; font-family: inherit;
    }
    .rf-btn:hover { opacity: .8; }
    .rf-apply  { background: var(--purple-dark); color: #fff; border-color: var(--purple-dark); }
    .rf-reset  { background: var(--card-bg); color: var(--text-sub); }
    .rf-export { background: #16a34a; color: #fff; border-color: #16a34a; }

    /* ── Stat mini-cards de estados ── */
    .estado-stats {
        display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 14px;
    }
    .es-card {
        background: var(--card-bg); border: 0.5px solid var(--card-border);
        border-radius: 10px; padding: 10px 14px; cursor: pointer; text-decoration: none;
        transition: transform .15s, box-shadow .15s; display: block;
    }
    .es-card:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,.08); }
    .es-card.active { border-width: 1.5px; }
    .es-icon  { font-size: 18px; margin-bottom: 4px; }
    .es-label { font-size: 10px; font-weight: 500; text-transform: uppercase;
                letter-spacing: .05em; color: var(--text-sub); margin-bottom: 2px; }
    .es-val   { font-size: 20px; font-weight: 700; }

    .range-chip {
        display: inline-flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 500;
        padding: 3px 10px; border-radius: 20px; background: var(--purple-bg);
        color: var(--purple-dark); white-space: nowrap;
    }
    .range-chip a { color: var(--purple-dark); text-decoration: none; font-size: 13px; }
</style>

{{-- ── FILTROS ── --}}
<form method="GET" action="{{ route('recepcionista.reservas.index') }}" class="res-wrap" id="res-filter-form">
<div class="res-filter">

    <div class="rf-group">
        <label class="rf-label"><i class="bi bi-search"></i> Buscar cliente</label>
        <input type="text" name="buscar" class="rf-input" style="min-width:190px"
               value="{{ $buscar ?? '' }}" placeholder="Nombre o documento…">
    </div>

    <div class="rf-divider"></div>

    <div class="rf-group">
        <label class="rf-label"><i class="bi bi-calendar3"></i> Check-in desde</label>
        <input type="date" name="desde" class="rf-input" value="{{ $desde ?? '' }}">
    </div>

    <div class="rf-group">
        <label class="rf-label"><i class="bi bi-calendar3"></i> Check-in hasta</label>
        <input type="date" name="hasta" class="rf-input" value="{{ $hasta ?? '' }}">
    </div>

    <div class="rf-divider"></div>

    <div class="rf-group">
        <label class="rf-label">Estado</label>
        <select name="estado" class="rf-select">
            <option value="">Todos</option>
            @foreach(['pendiente','confirmada','cancelada','finalizada'] as $e)
            <option value="{{ $e }}" {{ ($estado??'') === $e ? 'selected' : '' }}>
                {{ ucfirst($e) }}
            </option>
            @endforeach
        </select>
    </div>

    {{-- Accesos rápidos --}}
    <div class="rf-group">
        <label class="rf-label">Acceso rápido</label>
        <div style="display:flex;gap:6px">
            @foreach([
                ['label'=>'Hoy',    'desde'=>date('Y-m-d'),                       'hasta'=>date('Y-m-d')],
                ['label'=>'7 días', 'desde'=>date('Y-m-d',strtotime('-6 days')),  'hasta'=>date('Y-m-d')],
                ['label'=>'Mes',    'desde'=>date('Y-m-01'),                       'hasta'=>date('Y-m-d')],
            ] as $q)
            <a href="{{ route('recepcionista.reservas.index', ['desde'=>$q['desde'],'hasta'=>$q['hasta'],'estado'=>$estado??'']) }}"
               class="rf-btn rf-reset" style="font-size:12px;padding:0 10px">{{ $q['label'] }}</a>
            @endforeach
        </div>
    </div>

    <div class="rf-group" style="margin-left:auto">
        <label class="rf-label" style="visibility:hidden">.</label>
        <div style="display:flex;gap:6px">
            <button type="submit" class="rf-btn rf-apply">
                <i class="bi bi-funnel-fill" style="font-size:11px"></i> Filtrar
            </button>
            <a href="{{ route('recepcionista.reservas.index') }}" class="rf-btn rf-reset">
                <i class="bi bi-x-lg" style="font-size:11px"></i> Limpiar
            </a>
            <a href="{{ route('recepcionista.reservas.export-pdf', request()->query()) }}"
               class="rf-btn rf-export" target="_blank">
                <i class="bi bi-file-earmark-pdf" style="font-size:12px"></i> PDF
            </a>
        </div>
    </div>

    @if(!empty($desde) || !empty($hasta) || !empty($estado) || !empty($buscar))
    <div style="width:100%;display:flex;gap:8px;flex-wrap:wrap;padding-top:4px">
        @if(!empty($desde) || !empty($hasta))
        <div class="range-chip">
            <i class="bi bi-calendar3" style="font-size:10px"></i>
            @if($desde && $hasta)
                {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }} → {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}
            @elseif($desde)
                Desde {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }}
            @else
                Hasta {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}
            @endif
        </div>
        @endif
        @if(!empty($estado))
            @php $stateColors = ['confirmada'=>'#16a34a','pendiente'=>'#d97706','cancelada'=>'#dc2626','finalizada'=>'#0369a1']; @endphp
            <div class="range-chip" style="background:rgba({{ implode(',',sscanf($stateColors[$estado]??'#111','#%02x%02x%02x')) }},.12);color:{{ $stateColors[$estado] ?? '#111' }}">
                {{ ucfirst($estado) }}
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

{{-- ── CARDS DE ESTADO (clicables) ── --}}
<div class="estado-stats res-wrap">
    @php
        $estadoConfig = [
            'pendiente'  => ['color'=>'#d97706','bg'=>'#FEF3C7','icon'=>'bi-hourglass-split'],
            'confirmada' => ['color'=>'#16a34a','bg'=>'#DCFCE7','icon'=>'bi-check-circle-fill'],
            'cancelada'  => ['color'=>'#dc2626','bg'=>'#FEE2E2','icon'=>'bi-x-circle-fill'],
            'finalizada' => ['color'=>'#0369a1','bg'=>'#E0F2FE','icon'=>'bi-archive-fill'],
        ];
    @endphp
    @foreach($estadoConfig as $key => $cfg)
    <a href="{{ route('recepcionista.reservas.index', array_merge(request()->query(), ['estado' => ($estado??'')===$key ? '' : $key])) }}"
       class="es-card {{ ($estado??'')===$key ? 'active' : '' }}"
       style="{{ ($estado??'')===$key ? "border-color:{$cfg['color']};background:{$cfg['bg']}" : '' }}">
        <div class="es-icon" style="color:{{ $cfg['color'] }}"><i class="bi {{ $cfg['icon'] }}"></i></div>
        <div class="es-label">{{ ucfirst($key) }}</div>
        <div class="es-val" style="color:{{ ($estado??'')===$key ? $cfg['color'] : 'var(--text-main,#111)' }}">
            {{ $stats[$key.'s'] ?? $stats[$key] ?? 0 }}
        </div>
    </a>
    @endforeach
</div>

{{-- ── TABLA ── --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">
            <i class="bi bi-calendar-check" style="color:var(--accent2)"></i>
            Lista de Reservas
        </span>
        <span class="badge badge-muted">{{ $reservas->total() }} registros</span>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Habitación</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th style="text-align:center">Noches</th>
                    <th>Estado</th>
                    <th style="text-align:right">Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservas as $r)
                <tr>
                    <td class="mono" style="color:var(--muted)">{{ $r->id_reserva }}</td>
                    <td>
                        <div style="font-weight:500">
                            {{ $r->cliente->nombre }} {{ $r->cliente->apellido }}
                        </div>
                        <div style="font-size:11px;color:var(--muted)">{{ $r->cliente->documento }}</div>
                    </td>
                    <td>
                        @foreach($r->habitaciones as $h)
                        <span class="badge badge-info">{{ $h->numero }}</span>
                        @endforeach
                    </td>
                    <td class="mono">{{ $r->fecha_entrada->format('d/m/Y') }}</td>
                    <td class="mono">{{ $r->fecha_salida->format('d/m/Y') }}</td>
                    <td style="text-align:center;font-weight:500">
                        {{ $r->fecha_entrada->diffInDays($r->fecha_salida) }}
                    </td>
                    <td>
                        @php
                            $map = ['confirmada'=>'success','pendiente'=>'warning','cancelada'=>'danger','finalizada'=>'info'];
                        @endphp
                        <span class="badge badge-{{ $map[$r->estado] ?? 'muted' }}">{{ ucfirst($r->estado) }}</span>
                    </td>
                    <td style="font-weight:600;text-align:right">
                        S/ {{ number_format($r->precio_total, 2) }}
                    </td>
                    <td>
                        <div class="gap-2">
                            <a href="{{ route('recepcionista.reservas.show', $r) }}"
                               class="btn btn-ghost btn-sm btn-icon" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if(!in_array($r->estado, ['cancelada', 'finalizada']))
                            <a href="{{ route('recepcionista.reservas.edit', $r) }}"
                               class="btn btn-ghost btn-sm btn-icon" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;color:var(--muted);padding:40px">
                        <i class="bi bi-calendar-x" style="font-size:32px;display:block;margin-bottom:10px;opacity:.3"></i>
                        No hay reservas que coincidan con los filtros aplicados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="padding:14px 20px">{{ $reservas->withQueryString()->links() }}</div>
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