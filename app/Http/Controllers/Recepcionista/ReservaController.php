<?php

namespace App\Http\Controllers\Recepcionista;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Reserva;
use App\Models\Cliente;
use App\Models\Habitacion;
use App\Models\DetalleReserva;
use Barryvdh\DomPDF\Facade\Pdf;

class ReservaController extends Controller
{
    private function finalizarReservasVencidas()
    {
        $reservas = Reserva::whereIn('estado', ['pendiente', 'confirmada'])
            ->whereDate('fecha_salida', '<', today())
            ->get();

        foreach ($reservas as $reserva) {
            $reserva->update(['estado' => 'finalizada']);

            foreach ($reserva->habitaciones as $habitacion) {
                $habitacion->update(['estado' => 'disponible']);
            }

            $reserva->detalles()->update(['estado' => 'finalizada']);
        }
    }

    public function index(Request $request)
    {
        $this->finalizarReservasVencidas();

        $estado = $request->estado;
        $desde  = $request->desde;
        $hasta  = $request->hasta;
        $buscar = $request->buscar;

        $reservas = Reserva::with(['cliente', 'habitaciones.tipo'])
            ->when($estado, fn($q) => $q->where('estado', $estado))
            ->when($desde && $hasta, fn($q) => $q->whereBetween('fecha_entrada', [$desde, $hasta]))
            ->when($desde && !$hasta, fn($q) => $q->where('fecha_entrada', '>=', $desde))
            ->when(!$desde && $hasta, fn($q) => $q->where('fecha_entrada', '<=', $hasta))
            ->when($buscar, function ($q) use ($buscar) {
                $q->whereHas('cliente', function ($c) use ($buscar) {
                    $c->where('nombre', 'like', "%{$buscar}%")
                      ->orWhere('apellido', 'like', "%{$buscar}%")
                      ->orWhere('documento', 'like', "%{$buscar}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Stats rápidas para las cards
        $statsQuery = Reserva::query()
            ->when($desde && $hasta, fn($q) => $q->whereBetween('fecha_entrada', [$desde, $hasta]));

        $stats = [
            'pendientes'  => (clone $statsQuery)->where('estado', 'pendiente')->count(),
            'confirmadas' => (clone $statsQuery)->where('estado', 'confirmada')->count(),
            'canceladas'  => (clone $statsQuery)->where('estado', 'cancelada')->count(),
            'finalizadas' => (clone $statsQuery)->where('estado', 'finalizada')->count(),
        ];

        return view('recepcionista.reservas.index', compact(
            'reservas', 'estado', 'desde', 'hasta', 'buscar', 'stats'
        ));
    }

    public function exportPdf(Request $request)
    {
        $estado = $request->estado;
        $desde  = $request->desde;
        $hasta  = $request->hasta;
        $buscar = $request->buscar;

        $reservas = Reserva::with(['cliente', 'habitaciones.tipo', 'pagos'])
            ->when($estado, fn($q) => $q->where('estado', $estado))
            ->when($desde && $hasta, fn($q) => $q->whereBetween('fecha_entrada', [$desde, $hasta]))
            ->when($buscar, function ($q) use ($buscar) {
                $q->whereHas('cliente', function ($c) use ($buscar) {
                    $c->where('nombre', 'like', "%{$buscar}%")
                      ->orWhere('apellido', 'like', "%{$buscar}%");
                });
            })
            ->latest()
            ->get();

        $totalGeneral = $reservas->sum('precio_total');

        $pdf = Pdf::loadView('recepcionista.reservas.pdf', compact(
            'reservas', 'desde', 'hasta', 'estado', 'totalGeneral'
        ))->setPaper('a4', 'landscape');

        $filename = 'reservas_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }

    public function create()
    {
        $clientes     = Cliente::orderBy('apellido')->get();
        $habitaciones = Habitacion::with('tipo')->disponible()->get();

        return view('recepcionista.reservas.create', compact('clientes', 'habitaciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_cliente'    => 'required|exists:clientes,id_cliente',
            'id_habitacion' => 'required|exists:habitaciones,id_habitacion',
            'fecha_entrada' => 'required|date|after_or_equal:today',
            'fecha_salida'  => 'required|date|after:fecha_entrada',
            'num_huespedes' => 'required|integer|min:1',
            'notas'         => 'nullable|string',
        ]);

        $ocupada = DetalleReserva::where('id_habitacion', $request->id_habitacion)
            ->whereHas('reserva', function ($q) use ($request) {
                $q->whereIn('estado', ['pendiente', 'confirmada'])
                  ->where('fecha_entrada', '<', $request->fecha_salida)
                  ->where('fecha_salida',  '>', $request->fecha_entrada);
            })->exists();

        if ($ocupada) {
            return back()->withErrors([
                'id_habitacion' => 'La habitación no está disponible en esas fechas.'
            ])->withInput();
        }

        $habitacion = Habitacion::with('tipo')->findOrFail($request->id_habitacion);

        $noches = now()->parse($request->fecha_entrada)->diffInDays($request->fecha_salida);
        $precio = $habitacion->tipo->precio_base * $noches;

        DB::transaction(function () use ($request, $habitacion, $precio) {
            $reserva = Reserva::create([
                'id_cliente'     => $request->id_cliente,
                'id'             => Auth::id(),
                'fecha_entrada'  => $request->fecha_entrada,
                'fecha_salida'   => $request->fecha_salida,
                'num_huespedes'  => $request->num_huespedes,
                'precio_total'   => $precio,
                'estado'         => 'pendiente',
                'fecha_registro' => now(),
            ]);

            DetalleReserva::create([
                'id_reserva'      => $reserva->id_reserva,
                'id_habitacion'   => $habitacion->id_habitacion,
                'precio_aplicado' => $precio,
                'estado'          => 'activa',
            ]);

            $habitacion->update(['estado' => 'no disponible']);
        });

        return redirect()
            ->route('recepcionista.reservas.index')
            ->with('success', 'Reserva creada correctamente.');
    }

    public function show(Reserva $reserva)
    {
        $reserva->load(['cliente', 'usuario', 'habitaciones.tipo', 'pagos', 'boletas']);
        return view('recepcionista.reservas.show', compact('reserva'));
    }

    public function edit(Reserva $reserva)
    {
        if ($reserva->estado === 'cancelada') {
            return back()->withErrors(['error' => 'No se puede editar una reserva cancelada.']);
        }

        $reserva->load(['cliente', 'usuario', 'habitaciones.tipo', 'pagos']);

        $habitacionPrincipal = $reserva->habitaciones->first();
        $precioBase  = $habitacionPrincipal->tipo->precio_base;
        $totalPagado = $reserva->pagos->sum('monto');

        return view('recepcionista.reservas.edit', compact(
            'reserva', 'precioBase', 'totalPagado'
        ));
    }

    public function update(Request $request, Reserva $reserva)
    {
        $request->validate([
            'fecha_entrada' => 'required|date',
            'fecha_salida'  => 'required|date|after:fecha_entrada',
            'num_huespedes' => 'required|integer|min:1',
            'estado'        => 'required|in:pendiente,confirmada,cancelada,finalizada',
        ]);

        DB::transaction(function () use ($request, $reserva) {
            $habitacion  = $reserva->habitaciones->first();
            $noches      = \Carbon\Carbon::parse($request->fecha_entrada)->diffInDays($request->fecha_salida);
            $nuevoTotal  = $habitacion->tipo->precio_base * $noches;
            $totalPagado = $reserva->pagos->sum('monto');

            $nuevoEstado = $request->estado;
            if ($request->estado !== 'cancelada' && $nuevoTotal > $totalPagado) {
                $nuevoEstado = 'pendiente';
            }

            $reserva->update([
                'fecha_entrada' => $request->fecha_entrada,
                'fecha_salida'  => $request->fecha_salida,
                'num_huespedes' => $request->num_huespedes,
                'estado'        => $nuevoEstado,
                'precio_total'  => $nuevoTotal,
            ]);

            $reserva->detalles()->update(['precio_aplicado' => $nuevoTotal]);

            if ($request->estado === 'cancelada') {
                foreach ($reserva->habitaciones as $hab) {
                    $hab->update(['estado' => 'disponible']);
                }
                $reserva->detalles()->update(['estado' => 'cancelada']);
            }
        });

        return redirect()
            ->route('recepcionista.reservas.show', $reserva)
            ->with('success', 'Reserva actualizada correctamente.');
    }

    public function destroy(Reserva $reserva)
    {
        if ($reserva->estado === 'confirmada') {
            return back()->withErrors(['error' => 'No se puede eliminar una reserva confirmada.']);
        }

        DB::transaction(function () use ($reserva) {
            foreach ($reserva->habitaciones as $hab) {
                $hab->update(['estado' => 'disponible']);
            }
            $reserva->detalles()->delete();
            $reserva->delete();
        });

        return redirect()
            ->route('recepcionista.reservas.index')
            ->with('success', 'Reserva eliminada.');
    }
}