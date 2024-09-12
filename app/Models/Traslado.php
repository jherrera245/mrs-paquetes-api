<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Traslado extends Model
{
    use HasFactory;

    protected $table = 'traslados';

    protected $fillable = [
        'bodega_origen',
        'bodega_destino',
        'id_paquete',
        'numero_traslado',
        'fecha_traslado',
        'estado',
        'user_id'
    ];

    
    public function bodegaOrigen()
    {
        return $this->belongsTo(Bodegas::class, 'bodega_origen');
    }

    
    public function bodegaDestino()
    {
        return $this->belongsTo(Bodegas::class, 'bodega_destino');
    }

    
    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'id_paquete');
    }

    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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
