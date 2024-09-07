<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenRecoleccion extends Model
{
    use HasFactory;

    protected $table = 'ordenes_recolecciones';

    protected $fillable = [
        'id_ruta_recoleccion',
        'id_orden',
        'estado',
        'recoleccion_iniciada',
        'recoleccion_finalizada',
    ];

    public function rutaRecoleccion()
    {
        return $this->belongsTo(RutaRecoleccion::class, 'id_ruta_recoleccion');
    }

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'id_orden');
    }

    public function scopeOrdenesRecolecciones($query, $id_ruta_recoleccion)
    {
        return $query->where('id_ruta_recoleccion', $id_ruta_recoleccion);
    }

    public function scopeOrdenesRecoleccionesActivas($query, $id_ruta_recoleccion)
    {
        return $query->where('id_ruta_recoleccion', $id_ruta_recoleccion)->where('estado', 1);
    }
}
