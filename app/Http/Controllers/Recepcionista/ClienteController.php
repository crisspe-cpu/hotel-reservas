<?php

namespace App\Http\Controllers\Recepcionista;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cliente;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $clientes = Cliente::when($request->buscar, function ($q) use ($request) {
                        $q->where('nombre', 'like', "%{$request->buscar}%")
                          ->orWhere('apellido', 'like', "%{$request->buscar}%")
                          ->orWhere('documento', 'like', "%{$request->buscar}%");
                    })
                    ->orderBy('apellido')
                    ->paginate(15);

        return view('recepcionista.clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('recepcionista.clientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'         => 'required|string|max:100',
            'apellido'       => 'required|string|max:100',
            'tipo_documento' => 'required|in:dni,pasaporte,otro',
            'documento'      => 'required|string|max:20|unique:clientes,documento',
            'pais'           => 'nullable|string|max:80',
            'telefono'       => 'nullable|string|max:20',
        ]);

        $cliente = Cliente::create($request->only([
            'nombre', 'apellido', 'tipo_documento', 'documento', 'pais', 'telefono',
        ]));

        return redirect()->route('recepcionista.clientes.show', $cliente)
                         ->with('success', 'Cliente registrado correctamente.');
    }

    public function show(Cliente $cliente)
    {
        $cliente->load(['reservas' => fn($q) => $q->latest()->take(5)]);
        return view('recepcionista.clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('recepcionista.clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre'         => 'required|string|max:100',
            'apellido'       => 'required|string|max:100',
            'tipo_documento' => 'required|in:dni,pasaporte,otro',
            'documento'      => 'required|string|max:20|unique:clientes,documento,' . $cliente->id_cliente . ',id_cliente',
            'pais'           => 'nullable|string|max:80',
            'telefono'       => 'nullable|string|max:20',
        ]);

        $cliente->update($request->only([
            'nombre', 'apellido', 'tipo_documento', 'documento', 'pais', 'telefono',
        ]));

        return redirect()->route('recepcionista.clientes.show', $cliente)->with('success', 'Cliente actualizado.');
    }

    public function destroy(Cliente $cliente)
    {
        if ($cliente->reservas()->exists()) {
            return back()->withErrors(['error' => 'No se puede eliminar: el cliente tiene reservas registradas.']);
        }

        $cliente->delete();
        return redirect()->route('recepcionista.clientes.index')->with('success', 'Cliente eliminado.');
    }
}