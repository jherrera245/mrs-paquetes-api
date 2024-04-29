<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\tipo_personaController;
use App\Models\tipoPersona;

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
    Route::get('get_user', [AuthController::class, 'getUser']);
    Route::get('logout', [AuthController::class, 'logout']);

    //roles 
    Route::get('/roles', [RoleController::class, 'index']);
    Route::post('/roles', [RoleController::class, 'store']);
    Route::put('/roles/{role}', [RoleController::class, 'update']);
    Route::delete('/roles/{role}', [RoleController::class, 'destroy']);

    //permission 
    Route::get('/permission', [PermissionController::class, 'index']);
    Route::post('/permission', [PermissionController::class, 'store']);
    Route::put('/permission/{permission}', [PermissionController::class, 'update']);
    Route::delete('/permission/{permission}', [PermissionController::class, 'destroy']);

//tipopersona
    Route::get('/tipoPersona', [tipo_personaController::class, 'index']);
    Route::post('/tipoPersona', [tipo_personaController::class, 'store']);
    Route::put('/tipoPersona/{tipo_persona}', [tipo_personaController::class, 'update']);
    Route::delete('/tipoPersona/{tipo_persona}', [tipo_personaController::class, 'destroy']);

    //clientes
    Route::get('/clientes', [ClientesController::class, 'index']);
    Route::post('/clientes', [ClientesController::class, 'store']);
    Route::put('/clientes/{clientes}', [ClientesController::class, 'update']);
    Route::delete('/clientes/{clientes}', [ClientesController::class, 'destroy']);
    


});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});