<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Hotel System') — HotelApp</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --sidebar-w: 240px;
            --bg:        #0f1117;
            --surface:   #1a1d27;
            --surface2:  #222536;
            --border:    #2a2d3e;
            --accent:    #6c63ff;
            --accent2:   #a78bfa;
            --success:   #10b981;
            --warning:   #f59e0b;
            --danger:    #ef4444;
            --info:      #3b82f6;
            --text:      #e2e4f0;
            --muted:     #6b7280;
            --font:      'DM Sans', sans-serif;
            --mono:      'DM Mono', monospace;
        }

        body {
            font-family: var(--font);
            background: var(--bg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
        }

        /* ── SIDEBAR ───────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            z-index: 100;
            transition: transform .25s ease;
        }
        .sidebar-brand {
            padding: 24px 20px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .brand-icon {
            width: 34px; height: 34px;
            background: var(--accent);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }
        .brand-name {
            font-size: 15px;
            font-weight: 600;
            letter-spacing: -.02em;
            line-height: 1.2;
        }
        .brand-sub {
            font-size: 10px;
            color: var(--muted);
            font-family: var(--mono);
            text-transform: uppercase;
            letter-spacing: .06em;
        }
        .sidebar-section {
            padding: 20px 12px 8px;
        }
        .sidebar-label {
            font-size: 10px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .1em;
            padding: 0 8px;
            margin-bottom: 6px;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 400;
            color: var(--muted);
            text-decoration: none;
            transition: all .15s;
            margin-bottom: 2px;
        }
        .nav-link:hover { background: var(--surface2); color: var(--text); }
        .nav-link.active { background: rgba(108,99,255,.15); color: var(--accent2); font-weight: 500; }
        .nav-link i { font-size: 16px; width: 18px; text-align: center; }
        .sidebar-footer {
            margin-top: auto;
            padding: 16px 12px;
            border-top: 1px solid var(--border);
        }
        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            background: var(--surface2);
        }
        .user-avatar {
            width: 32px; height: 32px;
            background: var(--accent);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 600;
            flex-shrink: 0;
        }
        .user-name { font-size: 13px; font-weight: 500; line-height: 1.2; }
        .user-role { font-size: 10px; color: var(--accent2); font-family: var(--mono); text-transform: uppercase; }
        .logout-btn {
            margin-left: auto;
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            font-size: 15px;
            transition: color .15s;
        }
        .logout-btn:hover { color: var(--danger); }

        /* ── MAIN ──────────────────────────────────────── */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .topbar {
            padding: 16px 28px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--surface);
            position: sticky; top: 0; z-index: 50;
        }
        .page-title { font-size: 16px; font-weight: 600; }
        .page-breadcrumb { font-size: 12px; color: var(--muted); margin-top: 2px; }
        .topbar-actions { display: flex; gap: 10px; align-items: center; }
        .content { padding: 28px; flex: 1; }

        /* ── CARDS ─────────────────────────────────────── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
        }
        .card-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .card-title { font-size: 14px; font-weight: 600; }
        .card-body { padding: 20px; }

        /* ── STAT CARDS ────────────────────────────────── */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }
        .stat-icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .stat-label { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: .06em; margin-bottom: 4px; }
        .stat-value { font-size: 24px; font-weight: 600; line-height: 1; }
        .stat-sub { font-size: 11px; color: var(--muted); margin-top: 4px; }

        /* ── TABLE ─────────────────────────────────────── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        th {
            text-align: left;
            padding: 10px 16px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--muted);
            font-weight: 500;
            border-bottom: 1px solid var(--border);
        }
        td {
            padding: 13px 16px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }
        tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: var(--surface2); }

        /* ── BADGES ────────────────────────────────────── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
            font-family: var(--mono);
        }
        .badge-success  { background: rgba(16,185,129,.15); color: #34d399; }
        .badge-warning  { background: rgba(245,158,11,.15);  color: #fbbf24; }
        .badge-danger   { background: rgba(239,68,68,.15);   color: #f87171; }
        .badge-info     { background: rgba(59,130,246,.15);  color: #60a5fa; }
        .badge-muted    { background: var(--surface2); color: var(--muted); }

        /* ── BOTONES ───────────────────────────────────── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            font-family: var(--font);
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all .15s;
        }
        .btn-primary   { background: var(--accent);  color: #fff; }
        .btn-primary:hover { background: #5a52e0; }
        .btn-success   { background: var(--success); color: #fff; }
        .btn-success:hover { background: #059669; }
        .btn-danger    { background: transparent; border: 1px solid var(--border); color: var(--danger); }
        .btn-danger:hover { background: rgba(239,68,68,.1); }
        .btn-ghost     { background: var(--surface2); color: var(--text); border: 1px solid var(--border); }
        .btn-ghost:hover { background: var(--border); }
        .btn-sm { padding: 5px 10px; font-size: 12px; }
        .btn-icon { padding: 6px 8px; }

        /* ── FORMULARIOS ───────────────────────────────── */
        .form-grid { display: grid; gap: 16px; }
        .form-grid-2 { grid-template-columns: 1fr 1fr; }
        .form-grid-3 { grid-template-columns: 1fr 1fr 1fr; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        label { font-size: 12px; font-weight: 500; color: var(--muted); text-transform: uppercase; letter-spacing: .05em; }
        input, select, textarea {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 9px 12px;
            font-size: 13.5px;
            font-family: var(--font);
            color: var(--text);
            transition: border-color .15s;
            width: 100%;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(108,99,255,.1);
        }
        textarea { resize: vertical; min-height: 90px; }
        select option { background: var(--surface); }
        .form-error { font-size: 12px; color: var(--danger); margin-top: 2px; }
        .form-actions { display: flex; gap: 10px; justify-content: flex-end; padding-top: 8px; border-top: 1px solid var(--border); margin-top: 8px; }

        /* ── ALERTS ────────────────────────────────────── */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success { background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.3); color: #34d399; }
        .alert-danger  { background: rgba(239,68,68,.1);  border: 1px solid rgba(239,68,68,.3);  color: #f87171; }
        .alert-info    { background: rgba(59,130,246,.1);  border: 1px solid rgba(59,130,246,.3);  color: #60a5fa; }

        /* ── PAGINACIÓN ────────────────────────────────── */
        .pagination { display: flex; gap: 4px; align-items: center; margin-top: 16px; justify-content: flex-end; }
        .pagination a, .pagination span {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            border: 1px solid var(--border);
            color: var(--muted);
            text-decoration: none;
            background: var(--surface);
        }
        .pagination a:hover { background: var(--surface2); color: var(--text); }
        .pagination .active { background: var(--accent); color: #fff; border-color: var(--accent); }

        /* ── MISC ──────────────────────────────────────── */
        .divider { height: 1px; background: var(--border); margin: 20px 0; }
        .text-muted { color: var(--muted); font-size: 13px; }
        .text-accent { color: var(--accent2); }
        .mono { font-family: var(--mono); }
        .gap-2 { display:flex; gap: 8px; }
        .mb-4 { margin-bottom: 16px; }
        .mt-4 { margin-top: 16px; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main { margin-left: 0; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .form-grid-2, .form-grid-3 { grid-template-columns: 1fr; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- SIDEBAR --}}
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">🏨</div>
        <div>
            <div class="brand-name">HotelApp</div>
            <div class="brand-sub">Sistema de Reservas</div>
        </div>
    </div>

    {{-- Recepcionista y Admin --}}
    <div class="sidebar-section">
        <div class="sidebar-label">Principal</div>
        @if(auth()->user()->esAdmin())
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>
        @else
            <a href="{{ route('recepcionista.dashboard') }}" class="nav-link {{ request()->routeIs('recepcionista.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>
        @endif

        <a href="{{ route('recepcionista.reservas.index') }}" class="nav-link {{ request()->routeIs('reservas.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-check"></i> Reservas
        </a>
        <a href="{{ route('recepcionista.clientes.index') }}" class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Clientes
        </a>
        <a href="{{ route('recepcionista.pagos.index') }}" class="nav-link {{ request()->routeIs('pagos.*') ? 'active' : '' }}">
            <i class="bi bi-credit-card"></i> Pagos
        </a>
        <a href="{{ route('recepcionista.boletas.index') }}" class="nav-link {{ request()->routeIs('boletas.*') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i> Boletas
        </a>
    </div>

    {{-- Solo Admin --}}
    @if(auth()->user()->esAdmin())
    <div class="sidebar-section">
        <div class="sidebar-label">Administración</div>
        <a href="{{ route('admin.habitaciones.index') }}" class="nav-link {{ request()->routeIs('admin.habitaciones.*') ? 'active' : '' }}">
            <i class="bi bi-door-open"></i> Habitaciones
        </a>
        <a href="{{ route('admin.tipos.index') }}" class="nav-link {{ request()->routeIs('admin.tipos.*') ? 'active' : '' }}">
            <i class="bi bi-tags"></i> Tipos
        </a>
        <a href="{{ route('admin.usuarios.index') }}" class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
            <i class="bi bi-shield-lock"></i> Usuarios
        </a>
    </div>
    @endif

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->nombre }}</div>
                <div class="user-role">{{ auth()->user()->role }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin-left:auto">
                @csrf
                <button type="submit" class="logout-btn" title="Cerrar sesión">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- MAIN --}}
<div class="main">
    <div class="topbar">
        <div>
            <div class="page-title">@yield('page-title', 'Dashboard')</div>
            <div class="page-breadcrumb">@yield('breadcrumb', 'Inicio')</div>
        </div>
        <div class="topbar-actions">@yield('topbar-actions')</div>
    </div>

    <div class="content">
        {{-- Alertas globales --}}
        @if(session('success'))
            <div class="alert alert-success"><i class="bi bi-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if(session('info'))
            <div class="alert alert-info"><i class="bi bi-info-circle"></i> {{ session('info') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle"></i>
                <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            </div>
        @endif

        @yield('content')
    </div>
</div>

@stack('scripts')
</body>
</html>