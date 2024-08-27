<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TarifasDestinos;

class TarifasDestinosController extends Controller
{
    public function index()
    {
        // Obtener todas las tarifas_destinos con las relaciones
        $tarifasDestinos = TarifasDestinos::with(['tarifa', 'tamanoPaquete', 'departamento', 'municipio'])->get();

        // Formatear los resultados según el formato deseado
        $formattedData = $tarifasDestinos->map(function ($tarifaDestino) {
            return [
                'id' => $tarifaDestino->id,
                'monto' => $tarifaDestino->monto,
                'tarifa' => $tarifaDestino->tarifa->nombre,
                'tamano_paquete' =>$tarifaDestino->tamanoPaquete->nombre, 
                'departamento' => $tarifaDestino->departamento->nombre,
                'municipio'=> $tarifaDestino->municipio->nombre,

            ];
        });

        return response()->json($formattedData);
    }
}
