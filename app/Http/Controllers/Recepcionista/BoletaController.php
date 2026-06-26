<?php

namespace App\Http\Controllers\Recepcionista;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Boleta;
use App\Models\Reserva;
use Barryvdh\DomPDF\Facade\Pdf;

class BoletaController extends Controller
{
    public function index(Request $request)
    {
        $desde  = $request->desde;
        $hasta  = $request->hasta;
        $buscar = $request->buscar;

        $query = Boleta::with('reserva.cliente', 'usuario')
                       ->latest('fecha_emision');

        if ($desde && $hasta) {
            $query->whereBetween('fecha_emision', [
                $desde . ' 00:00:00',
                $hasta . ' 23:59:59',
            ]);
        } elseif ($desde) {
            $query->where('fecha_emision', '>=', $desde . ' 00:00:00');
        } elseif ($hasta) {
            $query->where('fecha_emision', '<=', $hasta . ' 23:59:59');
        }

        if ($buscar) {
            $query->whereHas('reserva.cliente', function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellido', 'like', "%{$buscar}%")
                  ->orWhere('documento', 'like', "%{$buscar}%");
            });
        }

        $totalGeneral = (clone $query)
        ->join('reservas', 'boletas.id_reserva', '=', 'reservas.id_reserva')
        ->join('pagos', 'reservas.id_reserva', '=', 'pagos.id_reserva')
        ->sum('pagos.monto');

        $boletas = $query->paginate(15)->withQueryString();

        return view('recepcionista.boletas.index', compact(
            'boletas', 'desde', 'hasta', 'buscar', 'totalGeneral'
        ));
    }

    public function exportPdf(Request $request)
    {
        $desde  = $request->desde;
        $hasta  = $request->hasta;
        $buscar = $request->buscar;

        $query = Boleta::with([
            'reserva.cliente',
            'reserva.pagos',
            'usuario'
        ])->latest('fecha_emision');

        if ($desde && $hasta) {
            $query->whereBetween('fecha_emision', [
                $desde . ' 00:00:00',
                $hasta . ' 23:59:59',
            ]);
        }
        if ($buscar) {
            $query->whereHas('reserva.cliente', function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellido', 'like', "%{$buscar}%");
            });
        }

        $boletas = $query->get();
        $totalGeneral = $boletas->sum(function ($boleta) {
            return $boleta->reserva->pagos->sum('monto');
        });

        $pdf = Pdf::loadView('recepcionista.boletas.pdf', compact(
            'boletas', 'desde', 'hasta', 'totalGeneral'
        ))->setPaper('a4', 'landscape');

        $filename = 'boletas_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_reserva' => 'required|exists:reservas,id_reserva',
        ]);

        $reserva = Reserva::with(['pagos', 'boletas'])
            ->findOrFail($request->id_reserva);

        if ($reserva->estado !== 'confirmada') {
            return back()->withErrors([
                'error' => 'Solo se puede emitir boleta de reservas confirmadas.'
            ]);
        }

        if ($reserva->boletas()->exists()) {
            return back()->withErrors([
                'error' => 'Esta reserva ya tiene una boleta emitida.'
            ]);
        }

        $totalPagado = $reserva->pagos()->sum('monto');

        if ($totalPagado < $reserva->precio_total) {
            return back()->withErrors([
                'error' => 'La reserva aún no ha sido pagada completamente.'
            ]);
        }

        $boleta = Boleta::create([
            'id_reserva'       => $reserva->id_reserva,
            'id'               => Auth::id(),
            'fecha_emision'    => now(),
            'total'            => $totalPagado,
            'total_acumulado'  => $totalPagado,
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

    /**
     * Exportar boleta individual en PDF (para imprimir/enviar al cliente)
     */
    public function exportBoletaPdf(Boleta $boleta)
    {
        $boleta->load('reserva.cliente', 'reserva.habitaciones.tipo', 'reserva.pagos', 'usuario');

        $pdf = Pdf::loadView('recepcionista.boletas.boleta_pdf', compact('boleta'))
                  ->setPaper('a4', 'portrait');

        $filename = 'boleta_' . $boleta->id_boleta . '_' . now()->format('Ymd') . '.pdf';
        return $pdf->download($filename);
    }
}