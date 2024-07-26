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

use App\Http\Controllers\ForgetPasswordController;
use App\Http\Controllers\ResetPasswordController;
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
 //
 Route::post('password/forget-password',[ForgetPasswordController::class, 'forgetPassword']);
 Route::post('password/reset',[ResetPasswordController::class, 'passwordReset']);

Route::group(['middleware' => ['jwt.verify', 'check.access']], function () {
    // Routes users api
    Route::get('auth/get_user', [AuthController::class, 'getUser'])->middleware('permission:auth-view_user');
    Route::get('auth/get_user_by_id/{id}', [AuthController::class, 'getUserById'])->middleware('permission:auth-get_user_by_id');
    Route::get('auth/get_users', [AuthController::class, 'getUsers'])->middleware('permission:auth-get_users');
    Route::post('auth/assign_user_role/{id}', [AuthController::class, 'assignUserRole'])->middleware('permission:auth-assign_user_role');
    Route::post('auth/assign_permissions_to_role/{id}', [AuthController::class, 'assignPermissionsToRole'])->middleware('permission:auth-assign_permissions_to_role');
    Route::get('auth/logout', [AuthController::class, 'logout']);
    Route::put('auth/update/{id}', [AuthController::class, 'update'])->middleware('permission:auth-update');
    Route::post('auth/store', [AuthController::class, 'store'])->middleware('permission:auth-store');
    Route::delete('auth/destroy/{id}', [AuthController::class, 'destroy'])->middleware('permission:auth-destroy');

    // Roles
    Route::get('roles', [RoleController::class, 'index'])->middleware('permission:roles-view');
    Route::post('roles', [RoleController::class, 'store'])->middleware('permission:roles-create');
    Route::put('roles/{role}', [RoleController::class, 'update'])->middleware('permission:roles-update');
    Route::post('roles/assign_permissions_role/{id}', [RoleController::class, 'assignPermissionsToRole'])->middleware('permission:roles-assign_permissions');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->middleware('permission:roles-destroy');

    // Permission
    Route::get('permission', [PermissionController::class, 'index'])->middleware('permission:permission-view');
    Route::post('permission', [PermissionController::class, 'store'])->middleware('permission:permission-create');
    Route::put('permission/{permission}', [PermissionController::class, 'update'])->middleware('permission:permission-update');
    Route::delete('permission/{permission}', [PermissionController::class, 'destroy'])->middleware('permission:permission-destroy');

    // Tipo Persona
    Route::get('tipoPersona', [TipoPersonaController::class, 'index'])->middleware('permission:tipoPersona-view');
    Route::post('tipoPersona', [TipoPersonaController::class, 'store'])->middleware('permission:tipoPersona-create');
    Route::put('tipoPersona/{tipo_persona}', [TipoPersonaController::class, 'update'])->middleware('permission:tipoPersona-update');
    Route::delete('tipoPersona/{tipo_persona}', [TipoPersonaController::class, 'destroy'])->middleware('permission:tipoPersona-destroy');

    // Clientes
    Route::get('clientes', [ClientesController::class, 'index'])->middleware('permission:clientes-view');
    Route::get('clientes/{cliente}', [ClientesController::class, 'show'])->middleware('permission:clientes-view');
    Route::post('clientes', [ClientesController::class, 'store'])->middleware('permission:clientes-create');
    Route::put('clientes/{clientes}', [ClientesController::class, 'update'])->middleware('permission:clientes-update');
    Route::delete('clientes/{clientes}', [ClientesController::class, 'destroy'])->middleware('permission:clientes-destroy');

    // Modelo Vehículo
    Route::get('modeloVehiculo', [ModeloVehiculoController::class, 'index'])->middleware('permission:modeloVehiculo-view');
    Route::get('modeloVehiculo/{modeloVehiculo}', [ModeloVehiculoController::class, 'show'])->middleware('permission:modeloVehiculo-show');
    Route::post('modeloVehiculo', [ModeloVehiculoController::class, 'store'])->middleware('permission:modeloVehiculo-create');
    Route::put('modeloVehiculo/{modeloVehiculo}', [ModeloVehiculoController::class, 'update'])->middleware('permission:modeloVehiculo-update');
    Route::delete('modeloVehiculo/{modeloVehiculo}', [ModeloVehiculoController::class, 'destroy'])->middleware('permission:modeloVehiculo-destroy');

    // Marca Vehículo
    Route::get('marcaVehiculo', [MarcaVehiculoController::class, 'index'])->middleware('permission:marcaVehiculo-view');
    Route::get('marcaVehiculo/{marcaVehiculo}', [MarcaVehiculoController::class, 'show'])->middleware('permission:marcaVehiculo-show');
    Route::post('marcaVehiculo', [MarcaVehiculoController::class, 'store'])->middleware('permission:marcaVehiculo-create');
    Route::put('marcaVehiculo/{marcaVehiculo}', [MarcaVehiculoController::class, 'update'])->middleware('permission:marcaVehiculo-update');
    Route::delete('marcaVehiculo/{marcaVehiculo}', [MarcaVehiculoController::class, 'destroy'])->middleware('permission:marcaVehiculo-destroy');

    // Vehículo
    Route::get('vehiculo', [VehiculoController::class, 'index'])->middleware('permission:vehiculo-view');
    Route::get('vehiculo/{vehiculo}', [VehiculoController::class, 'show'])->middleware('permission:vehiculo-show');
    Route::post('vehiculo', [VehiculoController::class, 'store'])->middleware('permission:vehiculo-create');
    Route::put('vehiculo/{vehiculo}', [VehiculoController::class, 'update'])->middleware('permission:vehiculo-update');
    Route::delete('vehiculo/{vehiculo}', [VehiculoController::class, 'destroy'])->middleware('permission:vehiculo-destroy');

    // Empleados
    Route::get('/empleados/relacion', [EmpleadoController::class, 'relacion']);
    Route::get('empleados', [EmpleadoController::class, 'index'])->middleware('permission:empleados-view');
    Route::get('empleados/{empleado}', [EmpleadoController::class, 'show'])->middleware('permission:empleados-show');
    Route::post('empleados', [EmpleadoController::class, 'store'])->middleware('permission:empleados-create');
    Route::put('empleados/{empleado}', [EmpleadoController::class, 'update'])->middleware('permission:empleados-update');
    Route::delete('empleados/{empleado}', [EmpleadoController::class, 'destroy'])->middleware('permission:empleados-destroy');

    // Rutas
    Route::resource('rutas', 'RutasController')->middleware('permission:rutas-view');
    Route::get('rutas', [RutasController::class, 'index'])->middleware('permission:rutas-view');
    Route::get('rutas/{ruta}', [RutasController::class, 'show'])->middleware('permission:rutas-show');
    Route::post('rutas', [RutasController::class, 'store'])->middleware('permission:rutas-create');
    Route::put('rutas/{ruta}', [RutasController::class, 'update'])->middleware('permission:rutas-update');
    Route::delete('rutas/{ruta}', [RutasController::class, 'destroy'])->middleware('permission:rutas-destroy');

    // Direcciones
    Route::resource('direcciones', 'DireccionesController')->middleware('permission:direcciones-view');
    Route::get('direcciones', [DireccionesController::class, 'index'])->middleware('permission:direcciones-view');
    Route::get('direcciones/{direccione}', [DireccionesController::class, 'show'])->middleware('permission:direcciones-show');
    Route::post('direcciones', [DireccionesController::class, 'store'])->middleware('permission:direcciones-create');
    Route::put('direcciones/{direccione}', [DireccionesController::class, 'update'])->middleware('permission:direcciones-update');
    Route::delete('direcciones/{direccione}', [DireccionesController::class, 'destroy'])->middleware('permission:direcciones-destroy');

    // Destinos
    Route::resource('destinos', 'DestinosController')->middleware('permission:destinos-view');
    Route::get('destinos', [DestinosController::class, 'index'])->middleware('permission:destinos-view');
    Route::get('destinos/{destino}', [DestinosController::class, 'show'])->middleware('permission:destinos-show');
    Route::post('destinos', [DestinosController::class, 'store'])->middleware('permission:destinos-create');
    Route::put('destinos/{destino}', [DestinosController::class, 'update'])->middleware('permission:destinos-update');
    Route::delete('destinos/{destino}', [DestinosController::class, 'destroy'])->middleware('permission:destinos-destroy');

    // Bodegas
    Route::resource('bodegas', 'BodegasController')->middleware('permission:bodegas-view');
    Route::get('bodegas', [BodegasController::class, 'index'])->middleware('permission:bodegas-view');
    Route::get('bodegas/{bodega}', [BodegasController::class, 'show'])->middleware('permission:bodegas-show');
    Route::post('bodegas', [BodegasController::class, 'store'])->middleware('permission:bodegas-create');
    Route::put('bodegas/{bodega}', [BodegasController::class, 'update'])->middleware('permission:bodegas-update');
    Route::delete('bodegas/{bodega}', [BodegasController::class, 'destroy'])->middleware('permission:bodegas-destroy');

    // Asignación Rutas
    Route::resource('asignacionrutas', 'AsignacionRutasController')->middleware('permission:asignacionrutas-view');
    Route::get('asignacionrutas', [AsignacionRutasController::class, 'index'])->middleware('permission:asignacionrutas-view');
    Route::get('asignacionrutas/{asignacionruta}', [AsignacionRutasController::class, 'show'])->middleware('permission:asignacionrutas-show');
    Route::post('asignacionrutas', [AsignacionRutasController::class, 'store'])->middleware('permission:asignacionrutas-create');
    Route::put('asignacionrutas/{asignacionruta}', [AsignacionRutasController::class, 'update'])->middleware('permission:asignacionrutas-update');
    Route::delete('asignacionrutas/{asignacionruta}', [AsignacionRutasController::class, 'destroy'])->middleware('permission:asignacionrutas-destroy');

    // Paquete
    Route::get('paquete', [PaqueteController::class, 'index'])->middleware('permission:paquete-view');
    Route::get('paquete/{paquete}', [PaqueteController::class, 'show'])->middleware('permission:paquete-show');
    Route::post('paquete', [PaqueteController::class, 'store'])->middleware('permission:paquete-create');
    Route::post('paquete/searchbyimage', [PaqueteController::class, 'searchByImage'])->middleware('permission:paquete-search_by_image');
    Route::put('paquete/{paquete}', [PaqueteController::class, 'update'])->middleware('permission:paquete-update');
    Route::delete('paquete/{paquete}', [PaqueteController::class, 'destroy'])->middleware('permission:paquete-destroy');
    Route::patch('paquete/{id}/restore', [PaqueteController::class, 'restore'])->middleware('permission:paquete-restore');

    // Historial Paquetes
    Route::get('historialpaquetes', [HistorialPaqueteController::class, 'index'])->middleware('permission:historialpaquetes-view');
    Route::get('historialpaquete/{paqueteId}', [HistorialPaqueteController::class, 'show'])->middleware('permission:historialpaquete-show');

    // Incidencias
    Route::get('incidencias', [IncidenciaController::class, 'index'])->middleware('permission:incidencias-view');
    Route::post('incidencias', [IncidenciaController::class, 'store'])->middleware('permission:incidencias-create');
    Route::get('incidencias/{incidencia}', [IncidenciaController::class, 'show'])->middleware('permission:incidencias-show');
    Route::put('incidencias/{incidencia}', [IncidenciaController::class, 'update'])->middleware('permission:incidencias-update');
    Route::delete('incidencias/{incidencia}', [IncidenciaController::class, 'destroy'])->middleware('permission:incidencias-destroy');


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
