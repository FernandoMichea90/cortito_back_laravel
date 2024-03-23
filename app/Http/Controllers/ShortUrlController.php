<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShortUrl;

class ShortUrlController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todas las URLs cortas
        $shortUrls = ShortUrl::all();
        
        // Retornar una respuesta JSON con las URLs cortas
        return response()->json($shortUrls);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos recibidos
        $request->validate([
            'short_url' => 'required|string|max:3',
            'long_url' => 'required|string|max:255',
        ]);
        
        // Crear una nueva URL corta con los datos recibidos
        $shortUrl = ShortUrl::create($request->all());
        
        // Retornar una respuesta JSON con la URL corta creada y un código 201 (Created)
        return response()->json($shortUrl, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Encontrar la URL corta por su ID
        $shortUrl = ShortUrl::findOrFail($id);
        
        // Retornar una respuesta JSON con la URL corta encontrada
        return response()->json($shortUrl);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Encontrar la URL corta por su ID
        $shortUrl = ShortUrl::findOrFail($id);
        
        // Validar los datos recibidos
        $request->validate([
            'short_url' => 'string|max:3',
            'long_url' => 'string|max:255',
        ]);
        
        // Actualizar los campos de la URL corta con los datos recibidos
        $shortUrl->update($request->all());
        
        // Retornar una respuesta JSON con un mensaje de éxito
        return response()->json(['message' => 'URL corta actualizada correctamente'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Encontrar la URL corta por su ID y eliminarla
        $shortUrl = ShortUrl::findOrFail($id);
        $shortUrl->delete();
        
        // Retornar una respuesta JSON con un código 204 (No Content) indicando que la URL corta ha sido eliminada
        return response()->json(null, 204);
    }
}
