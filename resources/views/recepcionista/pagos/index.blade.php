{{-- pagos/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Pagos')
@section('page-title', 'Pagos')
@section('breadcrumb', 'Inicio / Pagos')

@section('topbar-actions')
    <a href="{{ route('recepcionista.pagos.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Registrar Pago</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="bi bi-credit-card" style="color:var(--accent2)"></i> Historial de Pagos</span>
        <span class="badge badge-muted">{{ $pagos->total() }} registros</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>#</th><th>Reserva</th><th>Cliente</th><th>Fecha</th><th>Método</th><th>Monto</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($pagos as $p)
                <tr>
                    <td class="mono" style="color:var(--muted)">{{ $p->id_pago }}</td>
                    <td><a href="{{ route('recepcionista.reservas.show', $p->reserva) }}" style="color:var(--accent2)">#{{ $p->id_reserva }}</a></td>
                    <td>{{ $p->reserva->cliente->nombre }} {{ $p->reserva->cliente->apellido }}</td>
                    <td class="mono" style="font-size:12px">{{ $p->fecha_pago->format('d/m/Y H:i') }}</td>
                    <td><span class="badge badge-info">{{ $p->metodo_pago }}</span></td>
                    <td style="font-weight:600">S/ {{ number_format($p->monto, 2) }}</td>
                    <td>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:40px">Sin pagos registrados</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:14px 20px">{{ $pagos->links() }}</div>
</div>
@endsection