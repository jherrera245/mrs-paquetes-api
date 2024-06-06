<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\Genero;
use App\Models\Estado;
use App\Models\MarcaVehiculo;
use App\Models\Paquete;
use App\Models\Cargo;
use App\Models\Ruta;
use App\Models\Vehiculo;
use App\Models\Cliente;
use App\Models\Empleados;
use App\Models\Incidencia;
use App\Models\TipoPersona;
use App\Models\TipoIncidencia;
use App\Models\TipoPaquete;
use App\Models\Empaque;
use DB;

class DropdownController extends Controller
{
    public function getDepartamentos()
    {
        $departamentos = Departamento::all(); // Obtener todos los departamentos desde la base de datos

        return response()->json($departamentos);
    }

    public function getMunicipios($id){
       

       $municipio = DB::table('municipios')->select('id', 'nombre')->where('id_departamento', $id)->get();
       return response()->json(["municipio" => $municipio]);
    }

    public function getGeneros()
    {
        $generos = Genero::all();

        return response()-> json(["genero"=>$generos]);

    }

    public function getEstados()
    {
        $estados = Estado::all();

        return response()->json(["estado"=>$estados]);

    }

    public function getMarcas()
    {
        $marcas = MarcaVehiculo::all();

        return response()->json(["marcas"=>$marcas]);

    }

    public function getPaquetes()
    {
        $paquetes = Paquete::all();
        
        return response()->json(["paquetes"=>$paquetes]);
    }

    public function getCargos()
    {
        $cargos = Cargo::all();

        return response()->json(["cargos"=>$cargos]);

    }

    public function getRutas()
    {
        $rutas = Ruta::all();

        return response()->json(["rutas"=>$rutas]);
    }

    public function getVehiculo()
    {
        $vehiculo = Vehiculo::all();

        return response()->json(["vehiculo"=>$vehiculo]);
    }

    public function getCliente()
    {
        $clientes = Cliente::all();

        return response()->json(["cliente"=>$clientes]);
    }

    public function getEmpleados()
    {
        $empleados = Empleado::all();

        return response()->json(["empleado"=>$empleados]);

    }

    public function getIncidencia()
    {
        $incidencia = Incidencia::all();

        return response()->json(["incidencia"=>$incidencia]);
    }

    public function getTipoPersona()
    {
        $tipopersona = TipoPersona::all();

        return response()->json(["tipopersona"=>$tipopersona]);

    }

    public function getTipoIncidencia()
    {
        $tipoincidencia = TipoIncidencia::all();

        return response()->json(["tipoincidencia"=>$tipoincidencia]);

    }

    public function getTipoPaquetes()
    {
        $tipopaquete = TipoPaquete::all();

        return response()->json(["tipopaquete"=>$tipopaquete]);
    }

    public function getEmpaques()
    {
        $empaque = Empaque::all();

        return response()->json(["empaque"=>$empaque]);
    }

}


