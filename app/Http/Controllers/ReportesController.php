<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ReportesController extends Controller
{
    public function reporteAsignacionesRutasPorConductor(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'fecha' => 'required|date', 
            'id_conductor' => 'required'
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(),  Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    
        $fechaFormateada = Carbon::parse($request->input('fecha'))->format('Y-m-d');
        $fechaReporte = Carbon::parse($request->input('fecha'))->format('d-m-Y');
    
        $ruta = DB::table('asignacion_rutas')
            ->select('asignacion_rutas.id_ruta', 'asignacion_rutas.id_vehiculo', 'vehiculos.id_empleado_conductor', 'vehiculos.id_empleado_apoyo')
            ->join('rutas', 'rutas.id', '=', 'asignacion_rutas.id_ruta')
            ->join('vehiculos', 'vehiculos.id', '=', 'asignacion_rutas.id_vehiculo')
            ->where('rutas.fecha_programada', $fechaFormateada)
            ->where('vehiculos.id_empleado_conductor', $request->input('id_conductor'))
            ->first();

        if (!$ruta) {
            return response()->json(['error' => 'No hay información disponible para generar este reporte'],  Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $conductor = DB::table('empleados')->where('id', $ruta->id_empleado_conductor)->first();
        $apoyo = DB::table('empleados')->where('id', $ruta->id_empleado_apoyo)->first();
        $vehiculo = DB::table('vehiculos')->where('id', $ruta->id_vehiculo)->first();

        $data = DB::table('asignacion_rutas')
        ->select(
            'asignacion_rutas.prioridad',
            'asignacion_rutas.codigo_unico_asignacion',
            'paquetes.uuid',
            'paquetes.descripcion_contenido',
            'paquetes.peso',
            'departamento.nombre as departamento',
            'municipios.nombre as municipio',
            'asignacion_rutas.destino'
        )
        ->join('paquetes', 'paquetes.id', '=', 'asignacion_rutas.id_paquete')
        ->join('departamento', 'departamento.id', '=', 'asignacion_rutas.id_departamento')
        ->join('municipios', 'municipios.id', '=', 'asignacion_rutas.id_municipio')
        ->where('asignacion_rutas.id_ruta', $ruta->id_ruta);

        $totalAsignaciones = $data->count() ?? 0;
        $asignaciones = $data->orderBy('asignacion_rutas.prioridad', 'asc')->get();

        foreach($asignaciones as $asignacion)
        {
            //generando qrs de cada paquete
            $makeQr = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($asignacion->uuid)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->size(200)
            ->margin(10)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->build();

            $asignacion->qr_base64 = base64_encode($makeQr->getString());
        }
    
        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, "enable_php" => true, 'chroot' => public_path('images')])->loadView(
            'pdf.reporte_asigancion_rutas_por_conductor',
            [
                "fechaReporte" => $fechaReporte,
                "conductor" => $conductor,
                "apoyo" => $apoyo,
                "vehiculo" => $vehiculo,
                "asignaciones" => $asignaciones,
                "totalAsignaciones" => $totalAsignaciones
            ]
        );

        $output = $pdf->output();

        return response()->json(
            [
                'title' => 'Reporte_de_asignacion_de_rutas_por_conductor',
                'formart' => 'pdf',
                'base64Encode' => base64_encode($output)
            ],
            Response::HTTP_OK
        );
    }

    public function reporteRutasRecoleccionPorConductor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha' => 'required|date', 
            'id_conductor' => 'required'
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(),  Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    
        $fechaFormateada = Carbon::parse($request->input('fecha'))->format('Y-m-d');
        $fechaReporte = Carbon::parse($request->input('fecha'))->format('d-m-Y');
    
        $ruta = DB::table('ordenes_recolecciones')
            ->select('ordenes_recolecciones.id_ruta_recoleccion', 'rutas_recolecciones.id_vehiculo', 'vehiculos.id_empleado_conductor', 'vehiculos.id_empleado_apoyo')
            ->join('rutas_recolecciones', 'rutas_recolecciones.id', '=', 'ordenes_recolecciones.id_ruta_recoleccion')
            ->join('vehiculos', 'vehiculos.id', '=', 'rutas_recolecciones.id_vehiculo')
            ->where('rutas_recolecciones.fecha_asignacion', $fechaFormateada)
            ->where('vehiculos.id_empleado_conductor', $request->input('id_conductor'))
            ->first();

        if (!$ruta) {
            return response()->json(['error' => 'No hay información disponible para generar este reporte'],  Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $conductor = DB::table('empleados')->where('id', $ruta->id_empleado_conductor)->first();
        $apoyo = DB::table('empleados')->where('id', $ruta->id_empleado_apoyo)->first();
        $vehiculo = DB::table('vehiculos')->where('id', $ruta->id_vehiculo)->first();

        $recolecciones = DB::table('ordenes_recolecciones')
        ->select(
            'ordenes_recolecciones.id_orden',
            'ordenes_recolecciones.prioridad',
            'ordenes_recolecciones.codigo_unico_recoleccion',
            'departamento.nombre as departamento',
            'municipios.nombre as municipio',
            'ordenes_recolecciones.destino'
        )
        ->join('ordenes', 'ordenes.id', '=', 'ordenes_recolecciones.id_orden')
        ->join('departamento', 'departamento.id', '=', 'ordenes_recolecciones.id_departamento')
        ->join('municipios', 'municipios.id', '=', 'ordenes_recolecciones.id_municipio')
        ->where('ordenes_recolecciones.id_ruta_recoleccion', $ruta->id_ruta_recoleccion)
        ->get();

        $totalRecolecciones = 0; 

        foreach($recolecciones as $recoleccion)
        {

            $data = DB::table('detalle_orden')
            ->select(
                'paquetes.descripcion_contenido',
                'paquetes.peso',
                'empaquetado.empaquetado',
                'tamano_paquete.nombre as size'
            )
            ->join('paquetes', 'paquetes.id', '=', 'detalle_orden.id_paquete')
            ->join('empaquetado', 'empaquetado.id', '=', 'paquetes.id_empaque')
            ->join('tamano_paquete', 'tamano_paquete.id', '=', 'paquetes.id_tamano_paquete')
            ->where('detalle_orden.id_orden', $recoleccion->id_orden);

            $totalRecolecciones += $data->count() ?? 0;
            $detalles = $data->get();

            $recoleccion->paquetes = $detalles;
            $recoleccion->total_paquetes = $data->count();
        }
    
        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, "enable_php" => true, 'chroot' => public_path('images')])->loadView(
            'pdf.reporte_rutas_recoleccion_por_conductor',
            [
                "fechaReporte" => $fechaReporte,
                "conductor" => $conductor,
                "apoyo" => $apoyo,
                "vehiculo" => $vehiculo,
                "recolecciones" => $recolecciones,
                "totalRecolecciones" => $totalRecolecciones
            ]
        );

        return $pdf->stream('reporte' . '.pdf', array("Attachment" => false));
    }
}
