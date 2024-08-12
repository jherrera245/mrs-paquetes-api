<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\Genero;
use App\Models\Estado;
use App\Models\MarcaVehiculo;
use App\Models\Paquete;
use App\Models\Rutas;
use App\Models\Vehiculo;
use App\Models\Clientes;
use App\Models\Empleado;
use App\Models\Incidencia;
use App\Models\Bodegas;
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
        $generos = DB::table('genero')->select('id', 'nombre')->get();
        return response()->json(["generos" => $generos]);

    }
    public function getMarcas()
    {
        $marcas = DB::table('marcas')->select('id', 'nombre', 'descripcion')->get();
        return response()->json(["marcas" => $marcas]);
    }

    public function getModelos()
    {
        $modelos = DB::table('modelos')->select('id', 'nombre', 'descripcion')->get();
        return response()->json(["modelos" => $modelos]);
    }

    public function getModelosPorMarca($marcaId)
    {
        $modelos = DB::table('modelos')
        ->where('id_marca', $marcaId)
            ->select('id', 'nombre', 'descripcion')
            ->get();
        return response()->json(["modelos" => $modelos]);
    }

    public function getEstados()
    {
        $estado = DB::table('estados')->select('id', 'nombre','descripcion')->get();
        return response()->json(["estados" => $estado]);
    }

    public function getEstadoPaquete()
    {
        $estado_paquetes = DB::table('estado_paquetes')->select('id', 'nombre','descripcion')->get();
        return response()->json(["estado_paquetes" => $estado_paquetes]);
    }

    public function getPaquetes()
    {
        $paquetes = Paquete::all();
        return response()->json(["paquetes"=>$paquetes]);
    }

    public function getCargos()
    {
        $cargos = DB::table('cargos')->select('id', 'nombre','descripcion')->get();
        return response()->json(["cargos" => $cargos]);
    }

    public function getEstadoRutas()
    {
        $estado_rutas = DB::table('estado_rutas')->select('id', 'estado')->get();
        return response()->json(["estado_rutas" => $estado_rutas]);
    }

    public function getRutas()
    {
        $rutas = Rutas::all();

        return response()->json(["rutas"=>$rutas]);
    }

    public function getVehiculos()
    {
        $vehiculos = Vehiculo::all();

        return response()->json(["vehiculos"=>$vehiculo]);
    }

    public function getClientes()
    {
        $clientes = Clientes::all();

        return response()->json(["clientes"=>$clientes]);
    }

    public function getEmpleados()
    {
        $empleados = Empleado::all();

        return response()->json(["empleados"=>$empleados]);
    }

    public function getIncidencias()
    {
        $incidencias = Incidencia::all();

        return response()->json(["incidencias"=>$incidencias]);
    }

    public function getBodegas() 
    {
        $bodegas = Bodegas::all();
        return response()->json(["bodegas"=>$bodegas]);
    }

    public function getEstadoVehiculos()
    {
        $estado_vehiculos = DB::table('estado_vehiculos')->select('id', 'estado')->get();
        return response()->json(["estado_vehiculos" => $estado_vehiculos]);
    }

    public function getTipoPersona()
    {
        $tipo_persona = TipoPersona::all();
        return response()->json(["tipo_persona"=>$tipo_persona]);
    }

    public function getTipoIncidencia()
    {
        $tipo_incidencia = TipoIncidencia::all();
        return response()->json(["tipo_incidencia"=>$tipo_incidencia]);
    }

    public function getTipoPaquete()
    {
        $tipo_paquete = DB::table('tipo_paquete')->select('id', 'nombre','descripcion')->get();
        return response()->json(["tipo_paquete" => $tipo_paquete]);
    }

    public function getEmpaques()
    {
        $empaques = DB::table('empaquetado')->select('id', 'empaquetado')->get();
        return response()->json(["empaques" => $empaques]);
    }

    public function getEstadoClientes()
    {
        $estado_clientes = DB::table('estado_clientes')->select('id', 'estado')->get();
        return response()->json(["estado_clientes" => $estado_clientes]);
    }

    public function getEstadoEmpleados()
    {
        $estado_empleados = DB::table('estado_empleados')->select('id', 'estado')->get();
        return response()->json(["estado_empleados" => $estado_empleados]);
    }

    public function getEstadoIncidencias()
    {
        $estado_incidencias = DB::table('estado_incidencias')->select('id', 'estado')->get();
        return response()->json(["estado_incidencias" => $estado_incidencias]);
    }

    public function getPeopleData($type)
    {
        $data = [];

        if ($type == 0) {
            $empleados = DB::table('empleados')
            ->leftJoin('users', 'empleados.id', '=', 'users.id_empleado')
            ->whereNull('users.id_empleado')
            ->select('empleados.*')
            ->get();

            $data['empleados'] = $empleados;
        } else {
            $clientes= DB::table('clientes')
                ->leftJoin('users', 'clientes.id', '=', 'users.id_cliente')
                ->whereNull('users.id_cliente')
                ->select('clientes.*')
                ->get();
            $data['clientes'] = $clientes;
        }


        return response()->json($data);
    }
}


