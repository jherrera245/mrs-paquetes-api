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
use App\Http\Controllers\RutasController;
use App\Http\Controllers\BodegasController;
use App\Http\Controllers\DestinosController;
use App\Http\Controllers\DireccionesController;
use App\Http\Controllers\AsignacionRutasController;



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


Route::post('register', [AuthController::class, 'register']);
Route::post('login',  [AuthController::class, 'authenticate']);


Route::group(['middleware' => ['jwt.verify']], function() {
    //Routes users api
    Route::get('/auth/get_user', [AuthController::class, 'getUser']);
    Route::get('/auth/get_users', [AuthController::class, 'getUsers']);
    Route::post('/auth/assign_user_role/{id}', [AuthController::class, 'assignUserRole']);
    Route::get('/auth/logout', [AuthController::class, 'logout']);
    Route::put('/auth/update/{id}', [AuthController::class, 'update']);
    Route::put('/auth/update_cliente/{id}', [AuthController::class, 'updateCliente']);
    Route::post('/auth/store_cliente', [AuthController::class, 'storeCliente']);
    Route::put('/auth/update_empleado/{id}', [AuthController::class, 'updateEmpleado']);
    Route::post('/auth/store_empleado', [AuthController::class, 'storeEmpleado']);
    Route::delete('/auth/destroy/{id}', [AuthController::class, 'destroy']);

    //roles
    Route::get('/roles', [RoleController::class, 'index']);
    Route::post('/roles', [RoleController::class, 'store']);
    Route::put('/roles/{role}', [RoleController::class, 'update']);
    Route::post('/roles/assign_permissions_role/{id}', [RoleController::class, 'assignPermissionsToRole']);
    Route::delete('/roles/{role}', [RoleController::class, 'destroy']);

    //permission
    Route::get('/permission', [PermissionController::class, 'index']);
    Route::post('/permission', [PermissionController::class, 'store']);
    Route::put('/permission/{permission}', [PermissionController::class, 'update']);
    Route::delete('/permission/{permission}', [PermissionController::class, 'destroy']);

    //tipopersona
    Route::get('/tipoPersona', [TipoPersonaController::class, 'index']);
    Route::post('/tipoPersona', [TipoPersonaController::class, 'store']);
    Route::put('/tipoPersona/{tipo_persona}', [TipoPersonaController::class, 'update']);
    Route::delete('/tipoPersona/{tipo_persona}', [TipoPersonaController::class, 'destroy']);

    //clientes
    Route::get('/clientes', [ClientesController::class, 'index']);
    Route::post('/clientes', [ClientesController::class, 'store']);
    Route::put('/clientes/{clientes}', [ClientesController::class, 'update']);
    Route::delete('/clientes/{clientes}', [ClientesController::class, 'destroy']);

    //modeloVehiculo
    Route::get('/modeloVehiculo', [ModeloVehiculoController::class, 'index']);
    Route::get('/modeloVehiculo/{modeloVehiculo}', [ModeloVehiculoController::class, 'show']);
    Route::post('/modeloVehiculo', [ModeloVehiculoController::class, 'store']);
    Route::put('/modeloVehiculo/{modeloVehiculo}', [ModeloVehiculoController::class, 'update']);
    Route::delete('/modeloVehiculo/{modeloVehiculo}', [ModeloVehiculoController::class, 'destroy']);

    //marcaVehiculo
    Route::get('/marcaVehiculo', [MarcaVehiculoController::class, 'index']);
    Route::get('/marcaVehiculo/{marcaVehiculo}', [MarcaVehiculoController::class, 'show']);
    Route::post('/marcaVehiculo', [MarcaVehiculoController::class, 'store']);
    Route::put('/marcaVehiculo/{marcaVehiculo}', [MarcaVehiculoController::class, 'update']);
    Route::delete('/marcaVehiculo/{marcaVehiculo}', [MarcaVehiculoController::class, 'destroy']);

    //vehiculo
    Route::get('/vehiculo', [VehiculoController::class, 'index']);
    Route::get('/vehiculo/{vehiculo}', [VehiculoController::class, 'show']);
    Route::post('/vehiculo',[VehiculoController::class, 'store']);
    Route::put('/vehiculo/{vehiculo}', [VehiculoController::class, 'update']);
    Route::delete('/vehiculo/{vehiculo}', [VehiculoController::class, 'destroy']);

    //Empleados
   
    Route::get('/empleados', [EmpleadoController::class, 'index']);
    Route::get('/empleados/{empleado}', [EmpleadoController::class, 'show']);
    Route::post('/empleados', [EmpleadoController::class, 'store']);
    Route::put('/empleados/{empleado}', [EmpleadoController::class, 'update']);
    Route::delete('/empleados/{empleado}', [EmpleadoController::class, 'destroy']);

    //rutas
    Route::resource('rutas', 'RutasController');
    Route::get('/rutas', [RutasController::class, 'index']);
    Route::get('/rutas/{ruta}', [RutasController::class, 'show']);
    Route::post('/rutas', [RutasController::class, 'store']);
    Route::put('/rutas/{ruta}', [RutasController::class, 'update']);
    Route::delete('/rutas/{ruta}', [RutasController::class, 'destroy']);

    //direcciones
    Route::resource('direcciones', 'DireccionesController');
    Route::get('/direcciones', [DireccionesController::class, 'index']);
    Route::get('/direcciones/{direccione}', [DireccionesController::class, 'show']);
    Route::post('/direcciones', [DireccionesController::class, 'store']);
    Route::put('/direcciones/{direccione}', [DireccionesController::class, 'update']);
    Route::delete('/direcciones/{direccione}', [DireccionesController::class, 'destroy']);

    //destinos
    Route::resource('destinos', 'DestinosController');
    Route::get('/destinos', [DestinosController::class, 'index']);
    Route::get('/destinos/{destino}', [DestinosController::class, 'show']);
    Route::post('/destinos', [DestinosController::class, 'store']);
    Route::put('/destinos/{destino}', [DestinosController::class, 'update']);
    Route::delete('/destinos/{destino}', [DestinosController::class, 'destroy']);

    //bodegas
    Route::resource('bodegas', 'BodegasController');
    Route::get('/bodegas', [BodegasController::class, 'index']);
    Route::get('/bodegas/{bodega}', [BodegasController::class, 'show']);
    Route::post('/bodegas', [BodegasController::class, 'store']);
    Route::put('/bodegas/{bodega}', [BodegasController::class, 'update']);
    Route::delete('/bodegas/{bodega}', [BodegasController::class, 'destroy']);

    //asignarrutas
    Route::resource('asignacionrutas', 'AsignacionRutasController');
    Route::get('/asignacionrutas', [AsignacionRutasController::class, 'index']);
    Route::get('/asignacionrutas/{asignacionruta}', [AsignacionRutasController::class, 'show']);
    Route::post('/asignacionrutas', [AsignacionRutasController::class, 'store']);
    Route::put('/asignacionrutas/{asignacionruta}', [AsignacionRutasController::class, 'update']);
    Route::delete('/asignacionrutas/{asignacionruta}', [AsignacionRutasController::class, 'destroy']);
});
