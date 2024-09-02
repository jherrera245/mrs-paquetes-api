<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// importa el modeo RutaRecoleccion.
use App\Models\RutaRecoleccion;

class RutaRecoleccionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10); // 10 por defecto

        $rutasRecoleccion = RutaRecoleccion::with(['ruta', 'vehiculo', 'ordenesRecolecciones'])->paginate($perPage);

        // return json response.
        return response()->json($rutasRecoleccion);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_ruta' => 'required|exists:rutas,id',
            'id_vehiculo' => 'required|exists:vehiculos,id',
            'fecha_asignacion' => 'required|date',
        ]);

        $rutaRecoleccion = RutaRecoleccion::create($validatedData);
        return response()->json($rutaRecoleccion, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $rutaRecoleccion = RutaRecoleccion::with(['ruta', 'vehiculo', 'ordenesRecolecciones'])->findOrFail($id);
        
        return response()->json($rutaRecoleccion);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $validatedData = $request->validate([
            'id_ruta' => 'required|exists:rutas,id',
            'id_vehiculo' => 'required|exists:vehiculos,id',
            'fecha_asignacion' => 'required|date',
        ]);

        $rutaRecoleccion = RutaRecoleccion::findOrFail($id);
        $rutaRecoleccion->update($validatedData);

        return response()->json($rutaRecoleccion);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rutaRecoleccion = RutaRecoleccion::findOrFail($id);
        $rutaRecoleccion->delete();

        // retornar mensaje personalizado.
        return response()->json(['message' => 'Ruta de recolección eliminada correctamente.']);
    }
}
