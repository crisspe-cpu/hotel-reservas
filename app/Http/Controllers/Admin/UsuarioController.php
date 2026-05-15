<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::orderBy('name')->paginate(10);
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('admin.usuarios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'     => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'        => 'required|in:recepcionista,admin',
        ]);

        User::create([
            'name'     => $request->name,
            'email'     => $request->email,
            'password' => Hash::make($request->password),
            'role'        => $request->role,
            'estado'     => 'activo',
        ]);

        return redirect()->route('admin.usuarios.index') ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario)
    {
        return view('admin.usuarios.edit', [
            'usuario' => $usuario,
            'roles' => ['admin', 'recepcionista'],
            'estados' => ['activo', 'inactivo'],
        ]);
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email,' . $usuario->id,
            'role'        => 'required|in:recepcionista,admin',
            'estado'     => 'required|in:activo,inactivo',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $datos = $request->only(['name', 'email', 'role', 'estado']);
        if ($request->filled('password')) {
            $datos['password'] = Hash::make($request->password);
        }

        $usuario->update($datos);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        // Desactivar en lugar de eliminar para preservar historial 
        $usuario->update(['estado' => 'inactivo']);
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario desactivado.');
    }
}