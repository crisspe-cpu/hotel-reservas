<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Habitacion;
use App\Models\TipoHabitacion;

class HabitacionController extends Controller
{
    public function index()
    {
        $habitaciones = Habitacion::with('tipo')->orderBy('piso')->orderBy('numero')->paginate(15);
        return view('admin.habitaciones.index', compact('habitaciones'));
    }

    public function create()
    {
        $tipos = TipoHabitacion::orderBy('nombre')->get();
        return view('admin.habitaciones.create', compact('tipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero'              => 'required|string|max:10|unique:habitaciones,numero',
            'piso'                => 'required|integer|min:1',
            'estado'              => 'required|in:disponible,no disponible,mantenimiento',
            'id_tipo_habitacion'  => 'required|exists:tipo_habitaciones,id_tipo',
        ]);

        Habitacion::create($request->only(['numero', 'piso', 'estado', 'id_tipo_habitacion']));

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
        $tipos = TipoHabitacion::orderBy('nombre')->get();
        return view('admin.habitaciones.edit', compact('habitacion', 'tipos'));
    }

    public function update(Request $request, Habitacion $habitacion)
    {
        $request->validate([
            'numero'             => 'required|string|max:10|unique:habitaciones,numero,' . $habitacion->id_habitacion . ',id_habitacion',
            'piso'               => 'required|integer|min:1',
            'estado'             => 'required|in:disponible,no disponible,mantenimiento',
            'id_tipo_habitacion' => 'required|exists:tipo_habitaciones,id_tipo',
        ]);

        $habitacion->update($request->only(['numero', 'piso', 'estado', 'id_tipo_habitacion']));

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