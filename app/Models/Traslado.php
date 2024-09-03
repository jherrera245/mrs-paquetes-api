<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Traslado extends Model
{
    use HasFactory;

    protected $table = 'traslados';

    protected $fillable = [
        'id_bodega',
        'codigo_qr',
        'id_ubicacion_paquete',
        'id_asignacion_ruta',
        'id_orden',
        'numero_ingreso',
        'fecha_traslado',
        'estado',
    ];

    public function bodega()
    {
        return $this->belongsTo(Bodegas::class, 'id_bodega');
    }

    public function ubicacionPaquete()
    {
        return $this->belongsTo(UbicacionPaquete::class, 'id_ubicacion_paquete')->with('ubicacion');
    }

    public function asignacionRuta()
    {
        return $this->belongsTo(AsignacionRutas::class, 'id_asignacion_ruta');
    }

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'id_orden');
    }

    public function getFormattedData()
    {
        return [
            'id' => $this->id,
            'bodega' => $this->bodega ? $this->bodega->nombre : 'N/A',
            'id_bodega' => $this->bodega ? $this->bodega->id : 'N/A',
            'codigo_qr' => $this->codigo_qr,
            'ubicacion_paquete' => $this->ubicacionPaquete
                ? $this->ubicacionPaquete->ubicacion->nomenclatura: 'N/A',
            'id_ubicacion_paquete' => $this->ubicacionPaquete
                ? $this->ubicacionPaquete->id: 'N/A',
            'asignacion_ruta' => $this->asignacionRuta
                ? $this->asignacionRuta->codigo_unico_asignacion: 'N/A',
            'id_asignacion_ruta' => $this->asignacionRuta
                ? $this->asignacionRuta->id: 'N/A',
            'orden' => $this->orden
                ? $this->orden->concepto: 'N/A',
            'id_orden' => $this->orden
                ? $this->orden->id: 'N/A',
            'numero_ingreso' => $this->numero_ingreso,
            'fecha_traslado' => $this->fecha_traslado,
            'estado' => $this->estado,
        ];
    }
}
