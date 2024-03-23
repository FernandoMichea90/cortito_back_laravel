<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UrlDisponible;

class UrlDisponiblesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todas las URL disponibles
        $urlsDisponibles = UrlDisponible::all();
        
        // Retornar una respuesta JSON con las URL disponibles
        return response()->json($urlsDisponibles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos recibidos
        $request->validate([
            'short_url' => 'required|string|max:3',
            'url_disponible' => 'required|string|max:255',
            'disponible' => 'boolean',
        ]);
        
        // Crear una nueva URL disponible con los datos recibidos
        $urlDisponible = UrlDisponible::create($request->all());
        
        // Retornar una respuesta JSON con la URL disponible creada y un código 201 (Created)
        return response()->json($urlDisponible, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Encontrar la URL disponible por su ID
        $urlDisponible = UrlDisponible::findOrFail($id);
        
        // Retornar una respuesta JSON con la URL disponible encontrada
        return response()->json($urlDisponible);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Encontrar la URL disponible por su ID
        $urlDisponible = UrlDisponible::findOrFail($id);
        
        // Validar los datos recibidos
        $request->validate([
            'short_url' => 'string|max:3',
            'url_disponible' => 'string|max:255',
            'disponible' => 'boolean',
        ]);
        
        // Actualizar los campos de la URL disponible con los datos recibidos
        $urlDisponible->update($request->all());
        
        // Retornar una respuesta JSON con un mensaje de éxito
        return response()->json(['message' => 'URL disponible actualizada correctamente'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Encontrar la URL disponible por su ID y eliminarla
        $urlDisponible = UrlDisponible::findOrFail($id);
        $urlDisponible->delete();
        
        // Retornar una respuesta JSON con un código 204 (No Content) indicando que la URL disponible ha sido eliminada
        return response()->json(null, 204);
    }
}
