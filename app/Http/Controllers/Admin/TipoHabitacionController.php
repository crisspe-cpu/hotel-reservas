<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TipoHabitacion;

class TipoHabitacionController extends Controller
{
    public function index()
    {
        $tipos = TipoHabitacion::withCount('habitaciones')->orderBy('nombre')->get();
        return view('admin.tipos.index', compact('tipos'));
    }

    public function create()
    {
        return view('admin.tipos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:50|unique:tipo_habitaciones,nombre',
            'capacidad'   => 'required|integer|min:1|max:10',
            'precio_base' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
        ]);

        TipoHabitacion::create($request->only(['nombre', 'capacidad', 'precio_base', 'descripcion']));

        return redirect()->route('admin.tipos.index')
                         ->with('success', 'Tipo de habitación creado.');
    }

    public function edit(TipoHabitacion $tipo)
    {
        return view('admin.tipos.edit', compact('tipo'));
    }

    public function update(Request $request, TipoHabitacion $tipo)
    {
        $request->validate([
            'nombre'      => 'required|string|max:50|unique:tipo_habitaciones,nombre,' . $tipo->id_tipo . ',id_tipo',
            'capacidad'   => 'required|integer|min:1|max:10',
            'precio_base' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
        ]);

        $tipo->update($request->only(['nombre', 'capacidad', 'precio_base', 'descripcion']));

        return redirect()->route('admin.tipos.index')
                         ->with('success', 'Tipo de habitación actualizado.');
    }

    public function destroy(TipoHabitacion $tipo)
    {
        if ($tipo->habitaciones()->exists()) {
            return back()->withErrors(['error' => 'No se puede eliminar: tiene habitaciones asociadas.']);
        }

        $tipo->delete();
        return redirect()->route('admin.tipos.index')
                         ->with('success', 'Tipo de habitación eliminado.');
    }
}