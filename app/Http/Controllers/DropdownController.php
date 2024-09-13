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
use App\Models\Pasillo;
use App\Models\Ubicacion;
use App\Models\TipoPersona;
use App\Models\TipoIncidencia;
use App\Models\TipoPaquete;
use App\Models\Empaque;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use DB;

class DropdownController extends Controller
{
    public function getDepartamentos()
    {
        $departamentos = Departamento::all(); // Obtener todos los departamentos desde la base de datos

        return response()->json($departamentos);
    }

    public function getMunicipios($id)
    {


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
        $estado = DB::table('estados')->select('id', 'nombre', 'descripcion')->get();
        return response()->json(["estados" => $estado]);
    }

    public function getEstadoPaquete()
    {
        $estado_paquetes = DB::table('estado_paquetes')->select('id', 'nombre', 'descripcion')->get();
        return response()->json(["estado_paquetes" => $estado_paquetes]);
    }

    public function getPaquetes()
    {
        $paquetes = Paquete::all();
        return response()->json(["paquetes" => $paquetes]);
    }

    public function getCargos()
    {
        $cargos = DB::table('cargos')->select('id', 'nombre', 'descripcion')->get();
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

        return response()->json(["rutas" => $rutas]);
    }

    public function getVehiculos()
    {
        $vehiculos = Vehiculo::all();

        return response()->json(["vehiculos" => $vehiculos]);
    }

    public function getClientes()
    {
        $clientes = Clientes::all();

        return response()->json(["clientes" => $clientes]);
    }

    public function getEmpleados()
    {
        $empleados = Empleado::all();

        return response()->json(["empleados" => $empleados]);
    }

    public function getIncidencias()
    {
        $incidencias = Incidencia::all();

        return response()->json(["incidencias" => $incidencias]);
    }

    public function getPaquetesEnRecepcion()
    {
        try {
            // Filtrar los paquetes cuyo estado sea "En Recepción" (id_estado_paquete = 1)
            $paquetes = Paquete::select('id', 'uuid')
                ->where('id_estado_paquete', 1) // Filtrar por estado de paquete con id 1
                ->get();

            if ($paquetes->isEmpty()) {
                return response()->json(['message' => 'No se encontraron paquetes en estado "En Recepción".'], 404);
            }

            return response()->json(['paquetes' => $paquetes], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los paquetes en estado "En Recepción".',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPaquetesConDanio()
    {
        try {
            // Obtener los paquetes cuyo tipo de incidencia es "Daño" (id_tipo_incidencia = 2)
            $paquetes = Paquete::select('id', 'uuid') // Seleccionar el campo 'uuid'
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('incidencias')
                        ->whereColumn('paquetes.id', 'incidencias.id_paquete')
                        ->where('id_tipo_incidencia', 2); // Filtrar por tipo de incidencia "Daño"
                })
                ->whereNull('eliminado_at') // Excluir los eliminados
                ->whereNotExists(function ($query) {
                    // Verificar que el paquete no esté en la tabla 'ubicaciones_paquetes'
                    $query->select(DB::raw(1))
                        ->from('ubicaciones_paquetes')
                        ->whereColumn('paquetes.id', 'ubicaciones_paquetes.id_paquete');
                })
                ->get();

            if ($paquetes->isEmpty()) {
                return response()->json(['message' => 'No se encontraron paquetes con tipo de incidencia "Daño".'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['paquetes' => $paquetes], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los paquetes con tipo de incidencia "Daño".',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getBodegas()
    {
        $bodegas = Bodegas::all();
        return response()->json(["bodegas" => $bodegas]);
    }

    public function getEstadoVehiculos()
    {
        $estado_vehiculos = DB::table('estado_vehiculos')->select('id', 'estado')->get();
        return response()->json(["estado_vehiculos" => $estado_vehiculos]);
    }

    public function getTipoPersona()
    {
        $tipo_persona = TipoPersona::all();
        return response()->json(["tipo_persona" => $tipo_persona]);
    }

    public function getTipoIncidencia()
    {
        $tipo_incidencia = TipoIncidencia::all();
        return response()->json(["tipo_incidencia" => $tipo_incidencia]);
    }

    public function getTipoPaquete()
    {
        $tipo_paquete = DB::table('tipo_paquete')->select('id', 'nombre', 'descripcion')->get();
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
            $clientes = DB::table('clientes')
                ->leftJoin('users', 'clientes.id', '=', 'users.id_cliente')
                ->whereNull('users.id_cliente')
                ->select('clientes.*')
                ->get();
            $data['clientes'] = $clientes;
        }


        return response()->json($data);
    }

    // Función para que el conductor pueda ver la lista de las direcciones segun el id del cliente.
    public function getDirecciones($id)
    {
        // Obtener las direcciones del cliente, el nombre del municipio y el nombre del departamento.
        $direcciones = DB::table('direcciones')
            ->join('municipios', 'direcciones.id_municipio', '=', 'municipios.id')
            ->join('departamento', 'municipios.id_departamento', '=', 'departamento.id')
            ->where('id_cliente', $id)
            ->select('direcciones.id', 'direcciones.direccion', 'municipios.nombre as municipio', 'departamento.nombre as departamento')
            ->get();

        // Si no se encuentran direcciones, devolver un mensaje adecuado
        if ($direcciones->isEmpty()) {
            return response()->json(['error' => 'No se encontraron direcciones para este cliente'], 404);
        }

        // Devolver solamente las direcciones en un formato adecuado para su uso en un dropdown
        return response()->json($direcciones);
    }

    public function getGiros()
    {
        // Leer el archivo JSON desde el almacenamiento
        $json = file_get_contents(base_path('app/json/giros.json'));

        // Decodificar el JSON a un array PHP
        $giros = json_decode($json, true);

        // Verificar si la decodificación fue exitosa
        if (json_last_error() === JSON_ERROR_NONE) {
            // Devolver los datos en formato JSON
            return response()->json($giros);
        } else {
            // Manejar errores de decodificación
            return response()->json(['error' => 'Error al procesar el archivo JSON'], 500);
        }
    }

    public function getUbicacionesSinPaquetes()
    {
        try {
            // Obtener todas las ubicaciones que no están vinculadas a ninguna entrada en ubicaciones_paquetes
            // o que están vinculadas pero con un estado de 0 en la tabla ubicaciones_paquetes
            // Excluir aquellas ubicaciones cuya nomenclatura termine con "DA"
            $ubicaciones = Ubicacion::select('id', 'nomenclatura')
                ->where(function ($query) {
                    $query->whereDoesntHave('paquetes') // Ubicaciones sin paquetes
                        ->orWhereHas('paquetes', function ($query) {
                            $query->where('estado', 0); // Ubicaciones con paquetes en estado 0 en ubicaciones_paquetes
                        });
                })
                ->whereRaw("RIGHT(nomenclatura, 2) != 'DA'") // Excluir las ubicaciones cuya nomenclatura termine en 'DA'
                ->get();

            if ($ubicaciones->isEmpty()) {
                return response()->json(['message' => 'No se encontraron ubicaciones disponibles.'], Response::HTTP_NOT_FOUND);
            }

            return response()->json($ubicaciones, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las ubicaciones.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function getUbicacionesSinPaquetesDa()
    {
        try {
            // Obtener todas las ubicaciones que no están vinculadas a ninguna entrada en ubicaciones_paquetes
            // o que están vinculadas pero con un estado de 0 en la tabla ubicaciones_paquetes
            // Incluir solo aquellas ubicaciones cuya nomenclatura termine con "DA"
            $ubicaciones = Ubicacion::select('id', 'nomenclatura')
                ->where(function ($query) {
                    $query->whereDoesntHave('paquetes') // Ubicaciones sin paquetes
                        ->orWhereHas('paquetes', function ($query) {
                            $query->where('estado', 0); // Ubicaciones con paquetes en estado 0 en ubicaciones_paquetes
                        });
                })
                ->whereRaw("RIGHT(nomenclatura, 2) = 'DA'") // Incluir solo las ubicaciones cuya nomenclatura termine en 'DA'
                ->get();

            if ($ubicaciones->isEmpty()) {
                return response()->json(['message' => 'No se encontraron ubicaciones disponibles que terminen con "DA".'], Response::HTTP_NOT_FOUND);
            }

            return response()->json($ubicaciones, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las ubicaciones.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function getPasillosPorBodega($bodegaId)
    {
        try {
            // Obtener todos los pasillos que pertenecen a la bodega especificada
            $pasillos = Pasillo::where('id_bodega', $bodegaId)->get(['id', 'nombre']);

            if ($pasillos->isEmpty()) {
                return response()->json(['error' => 'No se encontraron pasillos para la bodega especificada.'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(["pasillos" => $pasillos], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los pasillos.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function getPaquetesSinAsignar(Request $request)
    {
        //pasar como parametro el id de la asignacion al editar
        $id_asignacion_ruta = $request->input('id_asignacion_ruta');

        //hacer la consulta
        $paquetes = DB::table('paquetes AS p')
            ->select('p.id', DB::raw("CONCAT('PAQUETE N#', p.id, ', [SEGUIMIENTO ', o.numero_seguimiento, ']') AS asignacion"))
            ->join('detalle_orden AS do', 'p.id', '=', 'do.id_paquete')
            ->join('ordenes AS o', 'o.id', '=', 'do.id_orden')
            ->leftJoin('asignacion_rutas AS ar', 'p.id', '=', 'ar.id_paquete')
            ->where(function ($query) use ($id_asignacion_ruta) {
                $query->whereNull('ar.id_paquete') // Paquetes sin asignar y Paquetes asignados a la ruta actual
                    ->orWhere('ar.id', $id_asignacion_ruta);
            })
            ->where(function ($query) use ($id_asignacion_ruta) {
                $query->whereNull('ar.id') // Paquetes no asignados a otras rutas
                    ->orWhere('ar.id', $id_asignacion_ruta); // O asignados solo a la ruta actual
            })
            ->get();

        return response()->json(['paquetes' => $paquetes], Response::HTTP_OK);
    }
}
