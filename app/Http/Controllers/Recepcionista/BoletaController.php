<?php

namespace App\Http\Controllers\Recepcionista;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Boleta;
use App\Models\Reserva;

class BoletaController extends Controller
{
    public function index()
    {
        $boletas = Boleta::with('reserva.cliente', 'usuario')
                         ->latest('fecha_emision')
                         ->paginate(15);

        return view('recepcionista.boletas.index', compact('boletas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_reserva' => 'required|exists:reservas,id_reserva',
        ]);

        $reserva = Reserva::with('pagos', 'boletas')
                        ->findOrFail($request->id_reserva);

        // Solo emitir si está confirmada
        if ($reserva->estado !== 'confirmada') {

            return back()->withErrors([
                'error' => 'Solo se puede emitir boleta de reservas confirmadas.'
            ]);
        }

        $reserva = Reserva::with('boletas')->findOrFail($request->id_reserva);

        // total actual de la reserva
        $totalPagado = $reserva->pagos()->sum('monto');
        // total ya facturado en boletas anteriores
        $totalFacturado = $reserva->boletas->sum('total');

        // diferencia real
        $montoNuevaBoleta = $totalPagado - $totalFacturado;

        if ($montoNuevaBoleta <= 0) {
            return back()->withErrors([
                'error' => 'No hay cambios para emitir boleta.'
            ]);
        }

            $boleta = Boleta::create([
                'id_reserva'    => $reserva->id_reserva,
                'id'            => Auth::id(),
                'fecha_emision' => now(),
                'total'         => $montoNuevaBoleta,
            ]);

        return redirect()
            ->route('recepcionista.boletas.show', $boleta)
            ->with('success', 'Boleta emitida correctamente.');
    }

    public function show(Boleta $boleta)
    {
        $boleta->load('reserva.cliente', 'reserva.habitaciones.tipo', 'reserva.pagos', 'usuario');
        return view('recepcionista.boletas.show', compact('boleta'));
    }
}