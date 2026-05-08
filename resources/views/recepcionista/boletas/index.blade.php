@extends('layouts.app')

@section('title', 'Boletas')
@section('page-title', 'Boletas')
@section('breadcrumb', 'Inicio / Boletas')

@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="bi bi-receipt" style="color:var(--accent2)"></i> Boletas Emitidas</span>
        <span class="badge badge-muted">{{ $boletas->total() }} registros</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>#</th><th>Reserva</th><th>Cliente</th><th>Emitido por</th><th>Fecha emisión</th><th>Total</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($boletas as $b)
                <tr>
                    <td class="mono" style="color:var(--muted)">{{ $b->id_boleta }}</td>
                    <td><a href="{{ route('recepcionista.reservas.show', $b->reserva) }}" style="color:var(--accent2)">#{{ $b->id_reserva }}</a></td>
                    <td>{{ $b->reserva->cliente->nombre }} {{ $b->reserva->cliente->apellido }}</td>
                    <td style="color:var(--muted)">{{ $b->usuario->nombre }}</td>
                    <td class="mono" style="font-size:12px">{{ $b->fecha_emision->format('d/m/Y H:i') }}</td>
                    <td style="font-weight:600; color:var(--success)">S/ {{ number_format($b->total, 2) }}</td>
                    <td><a href="{{ route('recepcionista.boletas.show', $b) }}" class="btn btn-ghost btn-sm btn-icon"><i class="bi bi-eye"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:40px">Sin boletas emitidas</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:14px 20px">{{ $boletas->links() }}</div>
</div>
@endsection