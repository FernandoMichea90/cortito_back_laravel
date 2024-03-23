<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;

class PersonaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $personas = Persona::all();
        return response()->json($personas);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $persona = Persona::findOrFail($id);
        return response()->json($persona);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'apellido' => 'required|string',
        ]);

        $persona = Persona::create($request->all());
        return response()->json($persona, 201);
    }



    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Encuentra la persona por su ID
        $persona = Persona::findOrFail($id);

        // Valida los datos recibidos
        $request->validate([
            'nombre' => 'required|string',
            'apellido' => 'required|string',
            // Agrega más reglas de validación según sea necesario
        ]);

        // Actualiza los campos de la persona con los datos recibidos
        $persona->update($request->all());

        // Retorna una respuesta de éxito
        return response()->json(['message' => 'Persona actualizada correctamente'], 200);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $persona = Persona::findOrFail($id);
        $persona->delete();
        return response()->json(null, 204);
    }
}
