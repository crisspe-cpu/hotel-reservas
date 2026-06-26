<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Habitacion;
use App\Models\TipoHabitacion;
use Illuminate\Support\Facades\DB;

class HabitacionController extends Controller
{
    public function index(Request $request)
    {
        $desde  = $request->desde;
        $hasta  = $request->hasta;
        $filtro = $request->filtro;
        $estado = $request->estado;

        $query = Habitacion::with('tipo');

        // Filtro por estado
        if ($estado) {
            $query->where('estado', $estado);
        }

        // Conteo de usos con filtro de fechas
        $query->withCount([
            'detallesReserva as detalles_reserva_count' => function ($q) use ($desde, $hasta) {
                $q->join('reservas', 'detalle_reservas.id_reserva', '=', 'reservas.id_reserva');
                if ($desde && $hasta) {
                    $q->whereBetween('reservas.fecha_registro', [
                        $desde . ' 00:00:00',
                        $hasta . ' 23:59:59',
                    ]);
                }
            },
        ]);

        if ($filtro === 'mas_usadas') {
            $query->orderByDesc('detalles_reserva_count');
        } else {
            $query->orderBy('piso')->orderBy('numero');
        }

        $habitaciones = $query->paginate(15)->withQueryString();

        // Totales para stats (sin paginación)
        $stats = Habitacion::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN estado = 'disponible'    THEN 1 ELSE 0 END) as disponibles,
            SUM(CASE WHEN estado = 'no disponible' THEN 1 ELSE 0 END) as ocupadas,
            SUM(CASE WHEN estado = 'mantenimiento' THEN 1 ELSE 0 END) as mantenimiento
        ")->first();

        return view('admin.habitaciones.index', compact(
            'habitaciones', 'stats', 'desde', 'hasta', 'filtro', 'estado'
        ));
    }

    public function create()
    {
        $tipos = TipoHabitacion::orderBy('nombre')->get();
        return view('admin.habitaciones.create', compact('tipos'));
    }

    public function store(Request $request)
    {
        $rules = [
            'numero'             => 'required|string|max:10|unique:habitaciones,numero',
            'piso'               => 'required|integer|min:1',
            'estado'             => 'required|in:disponible,no disponible,mantenimiento',
            'id_tipo_habitacion' => 'required|exists:tipo_habitaciones,id_tipo',
        ];

        if ($request->estado === 'mantenimiento') {
            $rules['motivo_mantenimiento'] = 'required|string|max:500';
            $rules['mantenimiento_desde']  = 'nullable|date';
            $rules['mantenimiento_hasta']  = 'nullable|date|after_or_equal:mantenimiento_desde';
        }

        $request->validate($rules);

        $data = $request->only(['numero', 'piso', 'estado', 'id_tipo_habitacion']);

        if ($request->estado === 'mantenimiento') {
            $data['motivo_mantenimiento'] = $request->motivo_mantenimiento;
            $data['mantenimiento_desde']  = $request->mantenimiento_desde;
            $data['mantenimiento_hasta']  = $request->mantenimiento_hasta;
        } else {
            $data['motivo_mantenimiento'] = null;
            $data['mantenimiento_desde']  = null;
            $data['mantenimiento_hasta']  = null;
        }

        Habitacion::create($data);

        return redirect()->route('admin.habitaciones.index')
                         ->with('success', 'Habitación registrada correctamente.');
    }

    public function show(Habitacion $habitacion)
    {
        $habitacion->load('tipo', 'reservas.cliente');
        return view('admin.habitaciones.show', compact('habitacion'));
    }

    public function edit(Habitacion $habitacion)
    {
        if ($habitacion->estado === 'no disponible') {
            return redirect()
                ->route('admin.habitaciones.show', $habitacion)
                ->withErrors(['error' => 'No se puede editar una habitación ocupada.']);
        }

        $tipos = TipoHabitacion::orderBy('nombre')->get();
        return view('admin.habitaciones.edit', compact('habitacion', 'tipos'));
    }

    public function update(Request $request, Habitacion $habitacion)
    {
        if ($habitacion->estado === 'no disponible') {
            return back()->withErrors(['error' => 'No se puede modificar una habitación ocupada.']);
        }

        $rules = [
            'numero'             => 'required|string|max:10|unique:habitaciones,numero,' . $habitacion->id_habitacion . ',id_habitacion',
            'piso'               => 'required|integer|min:1',
            'estado'             => 'required|in:disponible,no disponible,mantenimiento',
            'id_tipo_habitacion' => 'required|exists:tipo_habitaciones,id_tipo',
        ];

        if ($request->estado === 'mantenimiento') {
            $rules['motivo_mantenimiento'] = 'required|string|max:500';
            $rules['mantenimiento_desde']  = 'nullable|date';
            $rules['mantenimiento_hasta']  = 'nullable|date|after_or_equal:mantenimiento_desde';
        }

        $request->validate($rules);

        $data = $request->only(['numero', 'piso', 'estado', 'id_tipo_habitacion']);

        if ($request->estado === 'mantenimiento') {
            $data['motivo_mantenimiento'] = $request->motivo_mantenimiento;
            $data['mantenimiento_desde']  = $request->mantenimiento_desde;
            $data['mantenimiento_hasta']  = $request->mantenimiento_hasta;
        } else {
            // Limpiar campos de mantenimiento si ya no aplica
            $data['motivo_mantenimiento'] = null;
            $data['mantenimiento_desde']  = null;
            $data['mantenimiento_hasta']  = null;
        }

        $habitacion->update($data);

        return redirect()->route('admin.habitaciones.index')
                        ->with('success', 'Habitación actualizada.');
    }

    public function destroy(Habitacion $habitacion)
    {
        if ($habitacion->detallesReserva()->exists()) {
            return back()->withErrors(['error' => 'No se puede eliminar: tiene reservas asociadas.']);
        }

        $habitacion->delete();
        return redirect()->route('admin.habitaciones.index')
                         ->with('success', 'Habitación eliminada.');
    }
}