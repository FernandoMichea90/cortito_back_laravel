<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShortUrl;
use App\Models\UrlDisponible;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


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
        try {
            // Validar los datos recibidos
            $request->validate([
                'long_url' => 'required|string|max:255',
            ]);
    
            // Obtener un short url disponible
            $urlDisponible = UrlDisponible::getFirstUrlAvaible();
    
            // Obtener el usuario autenticado
            $user = Auth::user();
    
            // Crear una nueva URL corta con los datos recibidos
            $shortUrl = ShortUrl::create([
                'long_url' => $request->long_url,
                'persona_id' => $user->persona_id,
                'url_disponible_id' => $urlDisponible->id_url_disponible
            ]);
    
            // Actualizar el estado del URL disponible
            UrlDisponible::where('id_url_disponible', $shortUrl->url_disponible_id)->update(['disponible' => 0]);
    
            // Obtener la URL corta creada
            $createdShortUrl = UrlDisponible::find($shortUrl->url_disponible_id);
    
            // Estructurar la respuesta
            $response = [
                'message' => 'URL corta creada exitosamente',
                'short_url' => $createdShortUrl->short_url,
                'status' => 201
            ];
    
            // Retornar la respuesta JSON con el código 201 (Created)
            return response()->json($response, 201);
        } catch (ValidationException $e) {
            // Manejar errores de validación
            $errors = $e->errors();
            return response()->json(['message' => 'Error de validación', 'errors' => $errors], 422);
        } catch (\Exception $e) {
            // Manejar cualquier otro tipo de error
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
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
