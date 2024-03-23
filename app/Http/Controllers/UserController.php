<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todos los usuarios
        $users = User::all();
        
        // Retornar una respuesta JSON con los usuarios
        return response()->json($users);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Esto es para mostrar un formulario de creación, pero para una API no es necesario
        // Así que dejaremos esta función vacía
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos recibidos
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);
        
        // Crear un nuevo usuario con los datos recibidos
        $user = User::create($request->all());
        
        // Retornar una respuesta JSON con el usuario creado y un código 201 (Created)
        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Encontrar el usuario por su ID
        $user = User::findOrFail($id);
        
        // Retornar una respuesta JSON con el usuario encontrado
        return response()->json($user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Esto es para mostrar un formulario de edición, pero para una API no es necesario
        // Así que dejaremos esta función vacía
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Encontrar el usuario por su ID
        $user = User::findOrFail($id);
        
        // Validar los datos recibidos
        $request->validate([
            'name' => 'string',
            'email' => 'email|unique:users,email,'.$user->id,
            'password' => 'string|min:6',
        ]);
        
        // Actualizar los campos del usuario con los datos recibidos
        $user->update($request->all());
        
        // Retornar una respuesta JSON con un mensaje de éxito
        return response()->json(['message' => 'Usuario actualizado correctamente'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Encontrar el usuario por su ID y eliminarlo
        $user = User::findOrFail($id);
        $user->delete();
        
        // Retornar una respuesta JSON con un código 204 (No Content) indicando que el usuario ha sido eliminado
        return response()->json(null, 204);
    }
}
