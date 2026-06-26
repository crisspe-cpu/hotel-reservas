<?php

namespace App\Http\Controllers\Recepcionista;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pago;
use App\Models\Reserva;
use Barryvdh\DomPDF\Facade\Pdf;

class PagoController extends Controller
{
    public function index(Request $request)
    {
        $desde   = $request->desde;
        $hasta   = $request->hasta;
        $metodo  = $request->metodo_pago;
        $buscar  = $request->buscar;

        $query = Pago::with('reserva.cliente')
                     ->latest('fecha_pago');

        if ($desde && $hasta) {
            $query->whereBetween('fecha_pago', [
                $desde . ' 00:00:00',
                $hasta . ' 23:59:59',
            ]);
        } elseif ($desde) {
            $query->where('fecha_pago', '>=', $desde . ' 00:00:00');
        } elseif ($hasta) {
            $query->where('fecha_pago', '<=', $hasta . ' 23:59:59');
        }

        if ($metodo) {
            $query->where('metodo_pago', $metodo);
        }

        if ($buscar) {
            $query->whereHas('reserva.cliente', function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellido', 'like', "%{$buscar}%")
                  ->orWhere('documento', 'like', "%{$buscar}%");
            });
        }

        // Totales por método (para stats)
        $totalesPorMetodo = (clone $query)->select('metodo_pago', \DB::raw('SUM(monto) as total'))
            ->groupBy('metodo_pago')
            ->pluck('total', 'metodo_pago');

        $totalGeneral = (clone $query)->sum('monto');

        $pagos = $query->paginate(15)->withQueryString();

        return view('recepcionista.pagos.index', compact(
            'pagos', 'desde', 'hasta', 'metodo', 'buscar',
            'totalesPorMetodo', 'totalGeneral'
        ));
    }

    public function exportPdf(Request $request)
    {
        $desde  = $request->desde;
        $hasta  = $request->hasta;
        $metodo = $request->metodo_pago;
        $buscar = $request->buscar;

        $query = Pago::with('reserva.cliente')->latest('fecha_pago');

        if ($desde && $hasta) {

            $query->whereBetween('fecha_pago', [
                $desde . ' 00:00:00',
                $hasta . ' 23:59:59',
            ]);

        } elseif ($desde) {

            $query->where(
                'fecha_pago',
                '>=',
                $desde . ' 00:00:00'
            );

        } elseif ($hasta) {

            $query->where(
                'fecha_pago',
                '<=',
                $hasta . ' 23:59:59'
            );

        }
        if ($metodo) {
            $query->where('metodo_pago', $metodo);
        }
        if ($buscar) {
            $query->whereHas('reserva.cliente', function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellido', 'like', "%{$buscar}%");
            });
        }

        $pagos        = $query->get();
        $totalGeneral = $pagos->sum('monto');

        $pdf = Pdf::loadView('recepcionista.pagos.pdf', compact(
            'pagos', 'desde', 'hasta', 'metodo', 'totalGeneral'
        ))->setPaper('a4', 'landscape');

        $filename = 'pagos_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }

    public function create(Request $request)
    {
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

        $reserva     = Reserva::with('pagos')->findOrFail($request->id_reserva);
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

        $totalPagado += $request->monto;

        $reserva->update([
            'estado' => $totalPagado >= $reserva->precio_total
                ? 'confirmada'
                : 'pendiente'
        ]);

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

       if ($reserva->estado === 'finalizada') {
        return back()->withErrors([
            'error' => 'No se puede anular el pago de una reserva finalizada.'
        ]);
    }

        $pago->delete();

        $totalPagado = $reserva->pagos()->sum('monto');
        if ($totalPagado < $reserva->precio_total) {
            $reserva->update(['estado' => 'pendiente']);
        }

        return redirect()->route('recepcionista.reservas.show', $reserva)
                         ->with('success', 'Pago anulado.');
    }
}