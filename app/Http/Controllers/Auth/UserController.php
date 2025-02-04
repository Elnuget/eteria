<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Mostrar lista de usuarios
     */
    public function index()
    {
        $users = User::orderBy('id', 'desc')->get();
        return view('auth.users.index', compact('users'));
    }

    /**
     * Mostrar formulario para crear un nuevo usuario
     */
    public function create()
    {
        return view('auth.users.create');
    }

    /**
     * Almacenar un nuevo usuario
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'is_admin' => 'boolean',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone_number' => $request->phone_number,
            'is_admin' => $request->is_admin ?? false,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado exitosamente');
    }

    /**
     * Mostrar detalles de un usuario
     */
    public function show(User $user)
    {
        return view('auth.users.show', compact('user'));
    }

    /**
     * Mostrar formulario para editar un usuario
     */
    public function edit(User $user)
    {
        return view('auth.users.edit', compact('user'));
    }

    /**
     * Actualizar un usuario existente
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'is_admin' => 'boolean',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'is_admin' => $request->is_admin ?? false,
        ];

        if ($request->filled('password')) {
            $userData['password'] = bcrypt($request->password);
        }

        $user->update($userData);

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado exitosamente');
    }

    /**
     * Eliminar un usuario
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado exitosamente');
    }
} 