<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\TipoPersonaController;
use App\Http\Controllers\ModeloVehiculoController;
use App\Http\Controllers\MarcaVehiculoController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\DropdownController;
use App\Http\Controllers\RutasController;
use App\Http\Controllers\BodegasController;
use App\Http\Controllers\DestinosController;
use App\Http\Controllers\DireccionesController;
use App\Http\Controllers\AsignacionRutasController;
use App\Http\Controllers\PaqueteController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\HistorialPaqueteController;
use App\Http\Controllers\DetalleOrdenController;
use App\Http\Controllers\OrdenController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('login',  [AuthController::class, 'authenticate']);
Route::apiResource('detalle_orden', DetalleOrdenController::class);
Route::apiResource('ordenes', OrdenController::class);


Route::group(['middleware' => ['jwt.verify', 'check.access']], function () {
    //Routes users api
    Route::get('auth/get_user', [AuthController::class, 'getUser']);
    Route::get('auth/get_user_by_id/{id}', [AuthController::class, 'getUserById']);
    Route::get('auth/get_users', [AuthController::class, 'getUsers']);
    Route::post('auth/assign_user_role/{id}', [AuthController::class, 'assignUserRole']);
    Route::post('auth/assign_permissions_to_role/{id}', [AuthController::class, 'assignPermissionsToRole']);
    Route::get('auth/logout', [AuthController::class, 'logout']);
    Route::put('auth/update/{id}', [AuthController::class, 'update']);
    Route::post('auth/store', [AuthController::class, 'store']);
    Route::put('auth/update/{id}', [AuthController::class, 'update']);
    Route::delete('auth/destroy/{id}', [AuthController::class, 'destroy']);

    //roles
    Route::get('roles', [RoleController::class, 'index']);
    Route::post('roles', [RoleController::class, 'store']);
    Route::put('roles/{role}', [RoleController::class, 'update']);
    Route::post('roles/assign_permissions_role/{id}', [RoleController::class, 'assignPermissionsToRole']);
    Route::delete('roles/{role}', [RoleController::class, 'destroy']);

    //permission
    Route::get('permission', [PermissionController::class, 'index']);
    Route::post('permission', [PermissionController::class, 'store']);
    Route::put('permission/{permission}', [PermissionController::class, 'update']);
    Route::delete('permission/{permission}', [PermissionController::class, 'destroy']);

    //tipopersona
    Route::get('tipoPersona', [TipoPersonaController::class, 'index']);
    Route::post('tipoPersona', [TipoPersonaController::class, 'store']);
    Route::put('tipoPersona/{tipo_persona}', [TipoPersonaController::class, 'update']);
    Route::delete('tipoPersona/{tipo_persona}', [TipoPersonaController::class, 'destroy']);

    //clientes
    Route::get('clientes', [ClientesController::class, 'index']);
    Route::post('clientes', [ClientesController::class, 'store']);
    Route::put('clientes/{clientes}', [ClientesController::class, 'update']);
    Route::delete('clientes/{clientes}', [ClientesController::class, 'destroy']);

    //modeloVehiculo
    Route::get('modeloVehiculo', [ModeloVehiculoController::class, 'index']);
    Route::get('modeloVehiculo/{modeloVehiculo}', [ModeloVehiculoController::class, 'show']);
    Route::post('modeloVehiculo', [ModeloVehiculoController::class, 'store']);
    Route::put('modeloVehiculo/{modeloVehiculo}', [ModeloVehiculoController::class, 'update']);
    Route::delete('modeloVehiculo/{modeloVehiculo}', [ModeloVehiculoController::class, 'destroy']);

    //marcaVehiculo
    Route::get('marcaVehiculo', [MarcaVehiculoController::class, 'index']);
    Route::get('marcaVehiculo/{marcaVehiculo}', [MarcaVehiculoController::class, 'show']);
    Route::post('marcaVehiculo', [MarcaVehiculoController::class, 'store']);
    Route::put('marcaVehiculo/{marcaVehiculo}', [MarcaVehiculoController::class, 'update']);
    Route::delete('marcaVehiculo/{marcaVehiculo}', [MarcaVehiculoController::class, 'destroy']);

    //vehiculo
    Route::get('vehiculo', [VehiculoController::class, 'index']);
    Route::get('vehiculo/{vehiculo}', [VehiculoController::class, 'show']);
    Route::post('vehiculo', [VehiculoController::class, 'store']);
    Route::put('vehiculo/{vehiculo}', [VehiculoController::class, 'update']);
    Route::delete('vehiculo/{vehiculo}', [VehiculoController::class, 'destroy']);

    //Empleados
    Route::get('empleados', [EmpleadoController::class, 'index']);
    Route::get('empleados/{empleado}', [EmpleadoController::class, 'show']);
    Route::post('empleados', [EmpleadoController::class, 'store']);
    Route::put('empleados/{empleado}', [EmpleadoController::class, 'update']);
    Route::delete('empleados/{empleado}', [EmpleadoController::class, 'destroy']);

    //rutas
    Route::resource('rutas', 'RutasController');
    Route::get('rutas', [RutasController::class, 'index']);
    Route::get('rutas/{ruta}', [RutasController::class, 'show']);
    Route::post('rutas', [RutasController::class, 'store']);
    Route::put('rutas/{ruta}', [RutasController::class, 'update']);
    Route::delete('rutas/{ruta}', [RutasController::class, 'destroy']);

    //direcciones
    Route::resource('direcciones', 'DireccionesController');
    Route::get('direcciones', [DireccionesController::class, 'index']);
    Route::get('direcciones/{direccione}', [DireccionesController::class, 'show']);
    Route::post('direcciones', [DireccionesController::class, 'store']);
    Route::put('direcciones/{direccione}', [DireccionesController::class, 'update']);
    Route::delete('direcciones/{direccione}', [DireccionesController::class, 'destroy']);

    //destinos
    Route::resource('destinos', 'DestinosController');
    Route::get('destinos', [DestinosController::class, 'index']);
    Route::get('destinos/{destino}', [DestinosController::class, 'show']);
    Route::post('destinos', [DestinosController::class, 'store']);
    Route::put('destinos/{destino}', [DestinosController::class, 'update']);
    Route::delete('destinos/{destino}', [DestinosController::class, 'destroy']);

    //bodegas
    Route::resource('bodegas', 'BodegasController');
    Route::get('bodegas', [BodegasController::class, 'index']);
    Route::get('bodegas/{bodega}', [BodegasController::class, 'show']);
    Route::post('bodegas', [BodegasController::class, 'store']);
    Route::put('bodegas/{bodega}', [BodegasController::class, 'update']);
    Route::delete('bodegas/{bodega}', [BodegasController::class, 'destroy']);

    //asignarrutas
    Route::resource('asignacionrutas', 'AsignacionRutasController');
    Route::get('asignacionrutas', [AsignacionRutasController::class, 'index']);
    Route::get('asignacionrutas/{asignacionruta}', [AsignacionRutasController::class, 'show']);
    Route::post('asignacionrutas', [AsignacionRutasController::class, 'store']);
    Route::put('asignacionrutas/{asignacionruta}', [AsignacionRutasController::class, 'update']);
    Route::delete('asignacionrutas/{asignacionruta}', [AsignacionRutasController::class, 'destroy']);

    //paquete
    Route::get('paquete', [PaqueteController::class, 'index']);
    Route::get('paquete/{paquete}', [PaqueteController::class, 'show']);
    Route::post('paquete', [PaqueteController::class, 'store']);
    Route::post('paquete/searchbyimage', [PaqueteController::class, 'searchByImage']);
    Route::put('paquete/{paquete}', [PaqueteController::class, 'update']);
    Route::delete('paquete/{paquete}', [PaqueteController::class, 'destroy']);
    Route::patch('paquete/{id}/restore', [PaqueteController::class, 'restore']);

    //historial paquetes
    Route::get('historialpaquetes', [HistorialPaqueteController::class, 'index']);
    Route::get('historialpaquete/{paqueteId}', [HistorialPaqueteController::class, 'show']);
    
    //incidencias
    Route::get('incidencias', [IncidenciaController::class, 'index']);
    Route::post('incidencias', [IncidenciaController::class, 'store']);
    Route::get('incidencias/{incidencia}', [IncidenciaController::class, 'show']);
    Route::put('incidencias/{incidencia}', [IncidenciaController::class, 'update']);
    Route::delete('incidencias/{incidencia}', [IncidenciaController::class, 'destroy']);

    //Dropdown
    Route::get('dropdown/get_departamentos', [DropdownController::class, 'getDepartamentos']);
    Route::get('dropdown/get_municipio/{id}', [DropdownController::class, 'getMunicipios']);
    Route::get('dropdown/get_generos', [DropdownController::class, 'getGeneros']);
    Route::get('dropdown/get_marcas', [DropdownController::class, 'getMarcas']);
    Route::get('dropdown/get_estados', [DropdownController::class, 'getEstados']);
    Route::get('dropdown/get_estado_paquete', [DropdownController::class, 'getEstadoPaquete']);
    Route::get('dropdown/get_paquetes', [DropdownController::class, 'getPaquetes']);
    Route::get('dropdown/get_cargos', [DropdownController::class, 'getCargos']);
    Route::get('dropdown/get_estado_rutas', [DropdownController::class, 'getEstadoRutas']);
    Route::get('dropdown/get_rutas', [DropdownController::class, 'getRutas']);
    Route::get('dropdown/get_vehiculos', [DropdownController::class, 'getVehiculos']);
    Route::get('dropdown/get_clientes', [DropdownController::class, 'getClientes']);
    Route::get('dropdown/get_empleados', [DropdownController::class, 'getEmpleados']);
    Route::get('dropdown/get_incidencias', [DropdownController::class, 'getIncidencias']);
    Route::get('dropdown/get_bodegas', [DropdownController::class, 'getBodegas']);
    Route::get('dropdown/get_estado_vehiculos', [DropdownController::class, 'getEstadoVehiculos']);
    Route::get('dropdown/get_tipo_persona', [DropdownController::class, 'getTipoPersona']);
    Route::get('dropdown/get_tipo_incidencia', [DropdownController::class, 'getTipoIncidencia']);
    Route::get('dropdown/get_tipo_paquete', [DropdownController::class, 'getTipoPaquete']);
    Route::get('dropdown/get_empaques', [DropdownController::class, 'getEmpaques']);
    Route::get('dropdown/get_estado_clientes', [DropdownController::class, 'getEstadoClientes']);
    Route::get('dropdown/get_estado_empleados', [DropdownController::class, 'getEstadoEmpleados']);
    Route::get('dropdown/get_estado_incidencias', [DropdownController::class, 'getEstadoIncidencias']);
    Route::get('dropdown/get_people_data/{type}', [DropdownController::class, 'getPeopleData']);
});
