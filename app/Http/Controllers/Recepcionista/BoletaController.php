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

        $reserva = Reserva::with('pagos', 'cliente')->findOrFail($request->id_reserva);

        // Solo se emite boleta si la reserva está confirmada
        if ($reserva->estado !== 'confirmada') {
            return back()->withErrors(['error' => 'Solo se puede emitir boleta de reservas confirmadas.']);
        }

        // No emitir si ya tiene boleta
        if ($reserva->boleta) {
            return redirect()->route('boletas.show', $reserva->boleta)
                             ->with('info', 'Esta reserva ya tiene una boleta emitida.');
        }

        $totalPagado = $reserva->pagos->sum('monto');

        $boleta = Boleta::create([
            'id_reserva'    => $reserva->id_reserva,
            'id_user'       => Auth::id(),
            'fecha_emision' => now(),
            'total'         => $totalPagado,
        ]);

        // Marcar reserva como completada y liberar habitaciones
        $reserva->update(['estado' => 'cancelada']); // usar 'completada' si agregas ese ENUM
        foreach ($reserva->habitaciones as $hab) {
            $hab->update(['estado' => 'disponible']);
        }

        return redirect()->route('boletas.show', $boleta)
                         ->with('success', 'Boleta emitida correctamente.');
    }

    public function show(Boleta $boleta)
    {
        $boleta->load('reserva.cliente', 'reserva.habitaciones.tipo', 'reserva.pagos', 'usuario');
        return view('recepcionista.boletas.show', compact('boleta'));
    }
}