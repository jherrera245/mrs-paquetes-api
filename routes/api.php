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

});