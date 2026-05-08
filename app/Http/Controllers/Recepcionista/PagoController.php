<?php

namespace App\Http\Controllers\Recepcionista;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pago;
use App\Models\Reserva;

class PagoController extends Controller
{
    public function index()
    {
        $pagos = Pago::with('reserva.cliente')
                     ->latest('fecha_pago')
                     ->paginate(15);

        return view('recepcionista.pagos.index', compact('pagos'));
    }

    public function create(Request $request)
    {
        // Viene con id_reserva como query param: /pagos/create?id_reserva=5
        $reservas = Reserva::with('cliente')
                           ->whereIn('estado', ['pendiente', 'confirmada'])
                           ->get();

        $reservaSeleccionada = $request->id_reserva
            ? Reserva::with('cliente', 'pagos')->find($request->id_reserva)
            : null;

        return view('recepcionista.pagos.create', compact('reservas', 'reservaSeleccionada'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_reserva'  => 'required|exists:reservas,id_reserva',
            'monto'       => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:efectivo,tarjeta,yape,plin',
        ]);

        $reserva = Reserva::findOrFail($request->id_reserva);

        // Validar que no se pague de más
        $totalPagado = $reserva->pagos->sum('monto');
        $saldo       = $reserva->precio_total - $totalPagado;

        if ($request->monto > $saldo) {
            return back()->withErrors([
                'monto' => "El monto excede el saldo pendiente (S/ {$saldo}).",
            ])->withInput();
        }

        Pago::create([
            'id_reserva'  => $request->id_reserva,
            'monto'       => $request->monto,
            'fecha_pago'  => now(),
            'metodo_pago' => $request->metodo_pago,
        ]);

        // Confirmar reserva si está totalmente pagada
        if (($totalPagado + $request->monto) >= $reserva->precio_total) {
            $reserva->update(['estado' => 'confirmada']);
        }

        return redirect()->route('recepcionista.reservas.show', $reserva)
                         ->with('success', 'Pago registrado correctamente.');
    }

    public function show(Pago $pago)
    {
        $pago->load('reserva.cliente');
        return view('recepcionista.pagos.show', compact('pago'));
    }

    public function destroy(Pago $pago)
    {
        $reserva = $pago->reserva;

        if ($reserva->estado === 'completada') {
            return back()->withErrors(['error' => 'No se puede anular el pago de una reserva completada.']);
        }

        $pago->delete();

        // Si ya no hay pagos suficientes, volver a pendiente
        $totalPagado = $reserva->pagos()->sum('monto');
        if ($totalPagado < $reserva->precio_total) {
            $reserva->update(['estado' => 'pendiente']);
        }

        return redirect()->route('recepcionista.reservas.show', $reserva)
                         ->with('success', 'Pago anulado.');
    }
}