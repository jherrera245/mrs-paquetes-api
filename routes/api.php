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
use App\Http\Controllers\DireccionesController;
use App\Http\Controllers\AsignacionRutasController;
use App\Http\Controllers\PaqueteController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\HistorialPaqueteController;
use App\Http\Controllers\HistorialOrdenTrackingController;
use App\Http\Controllers\DetalleOrdenController;
use App\Http\Controllers\OrdenController;
use App\Http\Controllers\ForgetPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\VinetaController;
use App\Http\Controllers\TarifasDestinosController;
use App\Http\Controllers\RutaRecoleccionController;
use App\Http\Controllers\OrdenRecoleccionController;
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\UbicacionPaqueteController;
use App\Http\Controllers\PaqueteReporteController;
use App\Http\Controllers\TrasladoController;
use App\Http\Controllers\PasilloController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UbicacionPaqueteDaController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\KardexController;
// inventario
use App\Http\Controllers\InventarioController;
use Tymon\JWTAuth\Contracts\Providers\Auth;

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

Route::get('tarifa-destinos', [TarifasDestinosController::class, 'index']);
Route::post('login',  [AuthController::class, 'authenticate']);

//verificar email
Route::post('email-verification', [EmailVerificationController::class, 'email_verification']);
Route::post('send-email-verification', [EmailVerificationController::class, 'sendEmailVerification']);

Route::apiResource('detalle_orden', DetalleOrdenController::class);
Route::apiResource('ordenes', OrdenController::class);

//creacion del perfil del cliente
Route::post('crear-perfil-cliente', [AuthController::class, 'crearClientePerfil']);

//restablecer password
Route::post('password/forget-password', [ForgetPasswordController::class, 'forgetPassword']);
Route::post('password/reset', [ResetPasswordController::class, 'passwordReset']);
Route::get('seguimiento-orden', [OrdenController::class, 'buscarPorNumeroSeguimiento']);

//inicia sesion el cliente
Route::post('login-cliente', [AuthController::class, 'login_cliente']);
//registro de cliente
Route::post('register', [AuthController::class, 'register']);


Route::group(['middleware' => ['jwt.verify', 'check.access']], function () {

  Route::get('verificar-perfil', [AuthController::class, 'verificarPerfilExistente']);
  // Routes users api
  Route::get('auth/get_user', [AuthController::class, 'getUser']);
  Route::get('auth/get_user_by_id/{id}', [AuthController::class, 'getUserById'])->middleware('permission:auth-get_user_by_id');
  Route::get('auth/show/{id}', [AuthController::class, 'show']);
  Route::get('auth/get_users', [AuthController::class, 'getUsers'])->middleware('permission:auth-get_users');
  Route::post('auth/assign_user_role/{id}', [AuthController::class, 'assignUserRole'])->middleware('permission:auth-assign_user_role');
  Route::post('auth/assign_permissions_to_role/{id}', [AuthController::class, 'assignPermissionsToRole'])->middleware('permission:auth-assign_permissions_to_role');
  Route::get('auth/logout', [AuthController::class, 'logout']);
  Route::get('auth/logout-cliente', [AuthController::class, 'logoutCliente']);
  Route::put('auth/update/{id}', [AuthController::class, 'update'])->middleware('permission:auth-update');
  Route::post('auth/store', [AuthController::class, 'store'])->middleware('permission:auth-store');
  Route::delete('auth/destroy/{id}', [AuthController::class, 'destroy'])->middleware('permission:auth-destroy');
  Route::get('auth/get_assigned_permissions_to_role/{id}', [AuthController::class, 'getAssignedPermissionsToRole']);
  //registro del cliente
  Route::post('admin-registrar-cliente', [AuthController::class, 'adminClienteRegistrar'])->middleware('permission:auth-adminClienteRegistrar');
  Route::put('actualizar-perfil-cliente', [AuthController::class, 'actualizarClientePerfil'])->middleware('permission:auth-actualizarClientePerfil');
  Route::get('perfil-cliente', [AuthController::class, 'verPerfilCliente']);
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
  Route::get('clientes/estado_paquetes_cliente/{cliente}', [ClientesController::class, 'estado_paquetes_cliente'])->middleware('permission:clientes-view');
  Route::post('clientes', [ClientesController::class, 'store'])->middleware('permission:clientes-create');
  Route::put('clientes/{clientes}', [ClientesController::class, 'update'])->middleware('permission:clientes-update');
  Route::delete('clientes/{clientes}', [ClientesController::class, 'destroy'])->middleware('permission:clientes-destroy');

  // Modelo Vehículo
  Route::get('modeloVehiculo', [ModeloVehiculoController::class, 'index'])->middleware('permission:modeloVehiculo-view');
  Route::get('modeloVehiculo/{modeloVehiculo}', [ModeloVehiculoController::class, 'show'])->middleware('permission:modeloVehiculo-show');
  Route::post('modeloVehiculo', [ModeloVehiculoController::class, 'store'])->middleware('permission:modeloVehiculo-create');
  Route::put('modeloVehiculo/{modeloVehiculo}', [ModeloVehiculoController::class, 'update'])->middleware('permission:modeloVehiculo-update');
  Route::delete('modeloVehiculo/{modeloVehiculo}', [ModeloVehiculoController::class, 'destroy'])->middleware('permission:modeloVehiculo-destroy');

  // Modelo Ordenes
  Route::get('ordenes', [OrdenController::class, 'index'])->middleware('permission:orden-view');
  Route::get('ordenes/{ordenes}', [OrdenController::class, 'show'])->middleware('permission:orden-show');
  Route::post('ordenes', [OrdenController::class, 'store'])->middleware('permission:orden-create');
  //Route::put('ordenes/{ordenes}', [OrdenController::class, 'update'])->middleware('permission:orden-update');
  Route::delete('ordenes/{ordenes}', [OrdenController::class, 'destroy'])->middleware('permission:orden-destroy');
  Route::post('ordenes/{ordenes}/procesar-pago', [OrdenController::class, 'procesarPago'])->middleware('permission:orden-update');
  Route::get('ordenes/get_comprobante/{id}', [OrdenController::class, 'getComprobante'])->middleware('permission:orden-view');
  Route::get('ordenes/reenviar_comprobante/{id}', [OrdenController::class, 'reenviarComprobante']);
  Route::get('ordenes/{ordenes}/vineta', [VinetaController::class, 'generarVineta']);
  Route::post('ordenes-cliente', [OrdenController::class, 'ordenCliente'])->middleware('permission:orden-cliente');
  Route::get('ordenes-cliente/ver-ordenes', [OrdenController::class, 'misOrdenesCliente'])->middleware('permission:mis-ordenes-cliente');
  Route::put('ordenes/actualizar-orden/{id_orden}', [OrdenController::class, 'updateOrder'])->middleware('permission:orden-update');
  Route::put('ordenes/actualizar-detalle-orden/{id_detalle_orden}', [OrdenController::class, 'updateDetalleOrden'])->middleware('permission:orden-update');
  Route::post('ordenes/crear-detalle-orden/{id_orden}/{numero_seguimiento}', [OrdenController::class, 'createOrderDetailByOrdenId'])->middleware('permission:orden-create');

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
  Route::get('rutas', [RutasController::class, 'index'])->middleware('permission:rutas-view');
  Route::get('rutas/{ruta}', [RutasController::class, 'show'])->middleware('permission:rutas-show');
  Route::post('rutas', [RutasController::class, 'store'])->middleware('permission:rutas-create');
  Route::put('rutas/{ruta}', [RutasController::class, 'update'])->middleware('permission:rutas-update');
  Route::delete('rutas/{ruta}', [RutasController::class, 'destroy'])->middleware('permission:rutas-destroy');

  // Direcciones
  Route::get('direcciones', [DireccionesController::class, 'index'])->middleware('permission:direcciones-view');
  Route::get('direcciones/{direccione}', [DireccionesController::class, 'show'])->middleware('permission:direcciones-show');
  Route::post('direcciones', [DireccionesController::class, 'store'])->middleware('permission:direcciones-create');
  Route::put('direcciones/{direccione}', [DireccionesController::class, 'update'])->middleware('permission:direcciones-update');
  Route::delete('direcciones/{direccione}', [DireccionesController::class, 'destroy'])->middleware('permission:direcciones-destroy');

  // Bodegas
  Route::get('bodegas', [BodegasController::class, 'index'])->middleware('permission:bodegas-view');
  // existencias por bodega
  Route::get('bodegas/existencias', [BodegasController::class, 'existencias'])->middleware('permission:bodegas-view');
  Route::get('bodegas/{bodega}', [BodegasController::class, 'show'])->middleware('permission:bodegas-show');
  Route::post('bodegas', [BodegasController::class, 'store'])->middleware('permission:bodegas-create');
  Route::post('bodegas/agregarPaquete/', [BodegasController::class, 'agregarPaquete'])->middleware('permission:bodegas-create');
  Route::put('bodegas/{bodega}', [BodegasController::class, 'update'])->middleware('permission:bodegas-update');
  Route::delete('bodegas/{bodega}', [BodegasController::class, 'destroy'])->middleware('permission:bodegas-destroy');
  
  
  // Asignación Rutas
  Route::get('asignacionrutas', [AsignacionRutasController::class, 'index'])->middleware('permission:asignacionrutas-view');

  Route::get('asignacionrutas/estadoPaquetes/{id}', [AsignacionRutasController::class, 'estadoPaquetes'])->middleware('permission:asignacionrutas-view');
  Route::get('asignacionrutas/{asignacionruta}', [AsignacionRutasController::class, 'show'])->middleware('permission:asignacionrutas-show');
  Route::post('asignacionrutas', [AsignacionRutasController::class, 'store'])->middleware('permission:asignacionrutas-create');
  Route::put('asignacionrutas/{asignacionruta}', [AsignacionRutasController::class, 'update'])->middleware('permission:asignacionrutas-update');
  Route::delete('asignacionrutas/{asignacionruta}', [AsignacionRutasController::class, 'destroy'])->middleware('permission:asignacionrutas-destroy');
  Route::post('asignar-paquetes-ruta', [AsignacionRutasController::class, 'asignarRutasPaquetes']);
  // Paquete
  Route::get('paquete/filter-by-location', [PaqueteController::class, 'filterByLocation']);
  Route::get('paquete/get_paquetes_by_user', [PaqueteController::class, 'getPaquetesByUser']);
  Route::get('paquete/paquetes-asignables', [PaqueteController::class, 'paquetesAsignables'])->middleware('permission:paquete-view');
  Route::get('paquete', [PaqueteController::class, 'index'])->middleware('permission:paquete-view');
  Route::get('paquete/{paquete}', [PaqueteController::class, 'show'])->middleware('permission:paquete-show');
  Route::post('paquete', [PaqueteController::class, 'store'])->middleware('permission:paquete-create');
  Route::post('paquete/searchbyimage', [PaqueteController::class, 'searchByImage'])->middleware('permission:paquete-search_by_image');
  Route::put('paquete/{paquete}', [PaqueteController::class, 'update'])->middleware('permission:paquete-update');
  Route::delete('paquete/{paquete}', [PaqueteController::class, 'destroy'])->middleware('permission:paquete-destroy');
  Route::patch('paquete/{id}/restore', [PaqueteController::class, 'restore'])->middleware('permission:paquete-restore');
  Route::get('paquete/tracking-paquete/{paquete}', [PaqueteController::class, 'trackingPaquete']);

  //paquete_reporte
  Route::get('reporte-paquete', [PaqueteReporteController::class, 'index']);
  Route::get('reporte-paquete/{id}', [PaqueteReporteController::class, 'show']);
  Route::post('reporte-paquete', [PaqueteReporteController::class, 'store']);
  Route::put('reporte-paquete/{id}', [PaqueteReporteController::class, 'update']);
  Route::delete('reporte-paquete{id}', [PaqueteReporteController::class, 'destroy']);

  // Historial Paquetes
  Route::get('historialpaquetes', [HistorialPaqueteController::class, 'index'])->middleware('permission:historialpaquetes-view');
  Route::get('historialpaquete/{paqueteId}', [HistorialPaqueteController::class, 'show'])->middleware('permission:historialpaquete-show');

  // Incidencias
  Route::get('incidencias', [IncidenciaController::class, 'index'])->middleware('permission:incidencias-view');
  Route::post('incidencias', [IncidenciaController::class, 'store'])->middleware('permission:incidencias-create');
  Route::get('incidencias/{incidencia}', [IncidenciaController::class, 'show'])->middleware('permission:incidencias-show');
  Route::put('incidencias/{incidencia}', [IncidenciaController::class, 'update'])->middleware('permission:incidencias-update');
  Route::delete('incidencias/{incidencia}', [IncidenciaController::class, 'destroy'])->middleware('permission:incidencias-destroy');

  // Rutas para manejar ubicaciones de paquetes dañados con permisos específicos
  Route::get('ubicacion-paquetes-danados', [UbicacionPaqueteDaController::class, 'index'])->middleware('permission:ubicacion-paquetes-danados-index');
  Route::post('ubicacion-paquetes-danados', [UbicacionPaqueteDaController::class, 'store'])->middleware('permission:ubicacion-paquetes-danados-store');
  Route::get('ubicacion-paquetes-danados/{id}', [UbicacionPaqueteDaController::class, 'show'])->middleware('permission:ubicacion-paquetes-danados-show');
  Route::put('ubicacion-paquetes-danados/{id}', [UbicacionPaqueteDaController::class, 'update'])->middleware('permission:ubicacion-paquetes-danados-update');
  Route::delete('ubicacion-paquetes-danados/{id}', [UbicacionPaqueteDaController::class, 'destroy'])->middleware('permission:ubicacion-paquetes-danados-destroy');

  //Dropdown
  Route::get('dropdown/get_departamentos', [DropdownController::class, 'getDepartamentos']);
  Route::get('dropdown/get_municipio/{id}', [DropdownController::class, 'getMunicipios']);
  Route::get('dropdown/get_generos', [DropdownController::class, 'getGeneros']);
  Route::get('dropdown/get_marcas', [DropdownController::class, 'getMarcas']);
  Route::get('dropdown/get_modelos', [DropdownController::class, 'getModelos']);
  Route::get('dropdown/get_modelos/{marcaId}', [DropdownController::class, 'getModelosPorMarca']);
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
  Route::get('dropdown/get_direcciones/{id}', [DropdownController::class, 'getDirecciones']);
  Route::get('dropdown/giros', [DropdownController::class, 'getGiros']);
  Route::get('dropdown/get_pasillos/{bodegaId}', [DropdownController::class, 'getPasillosPorBodega']);
  Route::get('dropdown/get_Ubicaciones_SinPaquetes', [DropdownController::class, 'getUbicacionesSinPaquetes']);
  Route::get('dropdown/get_paquetes_sin_asignar', [DropdownController::class, 'getPaquetesSinAsignar']);
  Route::get('dropdown/get_Ubicaciones_paquetes_da', [DropdownController::class, 'getUbicacionesSinPaquetesDa']);
  Route::get('dropdown/get_paquetes_danio', [DropdownController::class, 'getPaquetesConDanio']);
  Route::get('dropdown/get_paquetes_en_recepcion', [DropdownController::class, 'getPaquetesEnRecepcion']);
  Route::get('dropdown/get_bodegas_moviles', [DropdownController::class, 'get_Bodegas_Moviles']);
  Route::get('dropdown/get_paquetes_usuario/{id_usuario}', [DropdownController::class, 'listarPaquetesPorUsuario']);


  // Detalle Orden
  Route::get('detalle-orden', [DetalleOrdenController::class, 'detalles_orden'])->middleware('permission:orden-view');
  Route::get('detalle-orden/{id}', [DetalleOrdenController::class, 'detalles_orden_id'])->middleware('permission:orden-show');
  Route::delete('detalle-orden/{id}', [OrdenController::class, 'destroyDetalleOrden'])->middleware('permission:orden-destroy');
  Route::put('detalle-orden/{id}', [DetalleOrdenController::class, 'update'])->middleware('permission:orden-update');
  Route::put('ordenes/{id}/cancelar', [OrdenController::class, 'cancelOrder'])->middleware('permission:orden-update');
  Route::get('ordenes/{id}/pdf', [OrdenController::class, 'generatePDF'])->middleware('permission:orden-view');
  Route::get('mis-ordenes', [OrdenController::class, 'misOrdenes'])->middleware('permission:mis-ordenes-cliente');
  Route::post('validacion-entrega', [DetalleOrdenController::class, 'validacionEntrega'])->middleware('permission:validacion-entrega');
  Route::post('finalizar-orden', [DetalleOrdenController::class, 'finalizarOrden'])->middleware('permission:validacion-entrega');
  
  // Historial Ordenes
  Route::get('historial/ordenes', [HistorialOrdenTrackingController::class, 'index'])->middleware('permission:historialpaquetes-view');
  Route::get('historial/{identificador}', [HistorialOrdenTrackingController::class, 'buscarHistorial'])->middleware('permission:historialpaquete-show');

  // rutas recolecciones.
  Route::get('rutas-recolecciones', [RutaRecoleccionController::class, 'index'])->middleware('permission:rutarecoleccion-view');
  Route::get('rutas-recolecciones/{id}', [RutaRecoleccionController::class, 'show'])->middleware('permission:rutarecoleccion-show');
  Route::post('rutas-recolecciones', [RutaRecoleccionController::class, 'store'])->middleware('permission:rutarecoleccion-create');
  Route::put('rutas-recolecciones/{id}', [RutaRecoleccionController::class, 'update'])->middleware('permission:rutarecoleccion-update');
  Route::delete('rutas-recolecciones/{id}', [RutaRecoleccionController::class, 'destroy'])->middleware('permission:rutarecoleccion-destroy');

  // Ubicaciones
  Route::get('ubicaciones', [UbicacionController::class, 'index'])->middleware('permission:ubicaciones-view');
  Route::get('ubicaciones/{id}', [UbicacionController::class, 'show'])->middleware('permission:ubicaciones-show');
  Route::post('ubicaciones', [UbicacionController::class, 'store'])->middleware('permission:ubicaciones-create');
  Route::put('ubicaciones/{id}', [UbicacionController::class, 'update'])->middleware('permission:ubicaciones-update');
  Route::delete('ubicaciones/{id}', [UbicacionController::class, 'destroy'])->middleware('permission:ubicaciones-destroy');

  // Rutas para el controlador UbicacionPaquete
  Route::get('ubicaciones-paquetes', [UbicacionPaqueteController::class, 'index'])->middleware('permission:ubicacionespaquetes-view');
  Route::get('ubicaciones-paquetes/filtro', [UbicacionPaqueteController::class, 'index_filtro']);
  Route::get('ubicaciones-paquetes/{id}', [UbicacionPaqueteController::class, 'show'])->middleware('permission:ubicacionespaquetes-show');
  Route::post('ubicaciones-paquetes', [UbicacionPaqueteController::class, 'store'])->middleware('permission:ubicacionespaquetes-create');
  Route::put('ubicaciones-paquetes/{id}', [UbicacionPaqueteController::class, 'update'])->middleware('permission:ubicacionespaquetes-update');
  Route::delete('ubicaciones-paquetes/{id}', [UbicacionPaqueteController::class, 'destroy'])->middleware('permission:ubicacionespaquetes-destroy');

  // Pasillos
  Route::get('/pasillos', [PasilloController::class, 'index'])->middleware('permission:pasillos-view');
  Route::get('/pasillos/{id}', [PasilloController::class, 'show'])->middleware('permission:pasillos-show');
  Route::post('/pasillos', [PasilloController::class, 'store'])->middleware('permission:pasillos-create');
  Route::put('/pasillos/{id}', [PasilloController::class, 'update'])->middleware('permission:pasillos-update');
  Route::delete('/pasillos/{id}', [PasilloController::class, 'destroy'])->middleware('permission:pasillos-destroy');

  // Traslados
  Route::get('traslados', [TrasladoController::class, 'index'])->middleware('permission:traslados-view');
  Route::post('traslados', [TrasladoController::class, 'store'])->middleware('permission:traslados-create');
  Route::get('traslados/{id}', [TrasladoController::class, 'show'])->middleware('permission:traslados-show');
  Route::put('traslados/{id}', [TrasladoController::class, 'update'])->middleware('permission:traslados-update');
  Route::delete('traslados/{id}', [TrasladoController::class, 'destroy'])->middleware('permission:traslados-destroy');
  Route::get('traslado-pdf/{id?}', [TrasladoController::class, 'trasladoPdf'])->middleware('permission:traslados-pdf');
  Route::get('traslado-pdf-general', [TrasladoController::class, 'trasladoPdfGeneral'])->middleware('permission:traslados-pdf');
  // eliminar detalle de traslado.
  Route::delete('traslados/destroy-detail/{id}', [TrasladoController::class, 'destroyDetail'])->middleware('permission:traslados-destroy');
  Route::post('finalizar-traslado', [TrasladoController::class, 'finalizarTraslado'])->middleware('permission:traslados-finalizar');

  // orden recoleccion.
  Route::get('orden-recoleccion', [OrdenRecoleccionController::class, 'index'])->middleware('permission:ordenrecoleccion-view');
  Route::get('orden-recoleccion/{id}', [OrdenRecoleccionController::class, 'show'])->middleware('permission:ordenrecoleccion-show');
  Route::post('orden-recoleccion', [OrdenRecoleccionController::class, 'store'])->middleware('permission:ordenrecoleccion-create');
  Route::put('orden-recoleccion/{id}', [OrdenRecoleccionController::class, 'update'])->middleware('permission:ordenrecoleccion-update');
  Route::post('orden-recoleccion/asignar-recoleccion/{id_orden_recoleccion}', [OrdenRecoleccionController::class, 'asignarRecoleccion'])->middleware('permission:ordenrecoleccion-update');
  Route::delete('orden-recoleccion/{id}', [OrdenRecoleccionController::class, 'destroy'])->middleware('permission:ordenrecoleccion-destroy');
  Route::post('orden-recoleccion/finalizar-orden-recoleccion/{id}', [OrdenRecoleccionController::class, 'finalizarOrdenRecoleccion'])->middleware('permission:ordenrecoleccion-create');

  Route::get('dashboard/card_summary', [DashboardController::class, 'cardSummary']);
  Route::get('dashboard/orders_by_day', [DashboardController::class, 'ordersByDay']);
  Route::get('dashboard/delivered_by_department', [DashboardController::class, 'deliveredByDepartment']);
  Route::get('dashboard/packages_by_status', [DashboardController::class, 'packagesByStatus']);
  Route::get('dashboard/last_orders', [DashboardController::class, 'lastOrders']);
  Route::get('dashboard/last_employees', [DashboardController::class, 'lastEmployees']);

  //reportes
  Route::post('reports/reporte_asignaciones_rutas_por_conductor', [ReportesController::class, 'reporteAsignacionesRutasPorConductor']);
  Route::post('reports/reporte_rutas_recoleccion_por_conductor', [ReportesController::class, 'reporteRutasRecoleccionPorConductor']);
  Route::post('reports/reporte_ventas', [ReportesController::class, 'reporteVentas']);

  // kardex.
  Route::get('kardex', [KardexController::class, 'index'])->middleware('permission:kardex-view');

  // inventario
  Route::get('inventario', [InventarioController::class, 'index'])->middleware('permission:inventario-view');
});
