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

class ReservaController extends Controller
{
    public function index(Request $request)
    {
        $reservas = Reserva::with(['cliente', 'habitaciones.tipo'])
                    ->when($request->estado, fn($q) => $q->where('estado', $request->estado))
                    ->when($request->fecha,  fn($q) => $q->whereDate('fecha_entrada', $request->fecha))
                    ->latest()
                    ->paginate(15);

        return view('recepcionista.reservas.index', compact('reservas'));
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
            'id_cliente'      => 'required|exists:clientes,id_cliente',
            'id_habitacion'   => 'required|exists:habitaciones,id_habitacion',
            'fecha_entrada'   => 'required|date|after_or_equal:today',
            'fecha_salida'    => 'required|date|after:fecha_entrada',
            'num_huespedes'   => 'required|integer|min:1',
            'notas'           => 'nullable|string',
        ]);

        // Verificar que la habitación no tenga reservas activas en esas fechas
        $ocupada = DetalleReserva::where('id_habitacion', $request->id_habitacion)
            ->whereHas('reserva', function ($q) use ($request) {
                $q->whereIn('estado', ['pendiente', 'confirmada'])
                  ->where('fecha_entrada', '<', $request->fecha_salida)
                  ->where('fecha_salida',  '>', $request->fecha_entrada);
            })->exists();

        if ($ocupada) {
            return back()->withErrors(['id_habitacion' => 'La habitación no está disponible en esas fechas.'])->withInput();
        }

        $habitacion = Habitacion::with('tipo')->findOrFail($request->id_habitacion);
        $noches     = now()->parse($request->fecha_entrada)->diffInDays($request->fecha_salida);
        $precio     = $habitacion->tipo->precio_base * $noches;

        DB::transaction(function () use ($request, $habitacion, $precio) {
            $reserva = Reserva::create([
                'id_cliente'     => $request->id_cliente,
                'id'        => Auth::id(),
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

            // Marcar habitación como no disponible
            $habitacion->update(['estado' => 'no disponible']);
        });

        return redirect()->route('recepcionista.reservas.index')->with('success', 'Reserva creada correctamente.');
    }

    public function show(Reserva $reserva)
    {
        $reserva->load(['cliente', 'usuario', 'habitaciones.tipo', 'pagos', 'boletas']);
        return view('recepcionista.reservas.show', compact('reserva'));
    }

    public function edit(Reserva $reserva)
    {
        if ($reserva->estado === 'cancelada') {
            return back()->withErrors([
                'error' => 'No se puede editar una reserva cancelada.'
            ]);
        }

        $reserva->load([
            'cliente',
            'usuario',
            'habitaciones.tipo',
            'pagos'
        ]);

        $habitacionPrincipal = $reserva->habitaciones->first();

        $precioBase = $habitacionPrincipal->tipo->precio_base;

        $totalPagado = $reserva->pagos->sum('monto');

        return view('recepcionista.reservas.edit', compact(
            'reserva',
            'precioBase',
            'totalPagado'
        ));
    }

    public function update(Request $request, Reserva $reserva)
    {
        $request->validate([
            'fecha_entrada'  => 'required|date',
            'fecha_salida'   => 'required|date|after:fecha_entrada',
            'num_huespedes'  => 'required|integer|min:1',
            'estado'         => 'required|in:pendiente,confirmada,cancelada',
        ]);

        DB::transaction(function () use ($request, $reserva) {

            $habitacion = $reserva->habitaciones->first();

            // Recalcular noches
            $noches = \Carbon\Carbon::parse($request->fecha_entrada)
                ->diffInDays($request->fecha_salida);

            // Nuevo total
            $nuevoTotal = $habitacion->tipo->precio_base * $noches;

            // Actualizar reserva
            $totalPagado = $reserva->pagos->sum('monto');

            $nuevoEstado = $request->estado;

            // Si debe dinero vuelve a pendiente
            if ($nuevoTotal > $totalPagado) {
                $nuevoEstado = 'pendiente';
            }

            $reserva->update([
                'fecha_entrada' => $request->fecha_entrada,
                'fecha_salida'  => $request->fecha_salida,
                'num_huespedes' => $request->num_huespedes,
                'estado'        => $nuevoEstado,
                'precio_total'  => $nuevoTotal,
            ]);

            // Actualizar detalle reserva
            $reserva->detalles()->update([
                'precio_aplicado' => $nuevoTotal,
            ]);

            // Si se cancela liberar habitaciones
            if ($request->estado === 'cancelada') {

                foreach ($reserva->habitaciones as $habitacion) {

                    $habitacion->update([
                        'estado' => 'disponible'
                    ]);
                }

                $reserva->detalles()->update([
                    'estado' => 'cancelada'
                ]);
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

        return redirect()->route('recepcionista.reservas.index')
                         ->with('success', 'Reserva eliminada.');
    }
}