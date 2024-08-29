<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialOrdenTracking extends Model
{
    use HasFactory;

    protected $table = 'historial_ordenes_tracking';

    protected $fillable = [
        'id_orden',
        'numero_seguimiento',
        'id_estado_paquete',
        'fecha_hora',
        'comentario'
    ];

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'id_orden');
    }

    public function estadoPaquete()
    {
        return $this->belongsTo(EstadoPaquete::class, 'id_estado_paquete');
    }
}