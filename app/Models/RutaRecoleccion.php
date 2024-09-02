<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RutaRecoleccion extends Model
{
    use HasFactory;

    protected $table = 'rutas_recolecciones';

    protected $fillable = [
        'id_ruta',
        'id_vehiculo',
        'fecha_asignacion',
        'estado'
    ];

    public function ruta()
    {
        return $this->belongsTo(Rutas::class, 'id_ruta');
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'id_vehiculo');
    }

    // relacion con orden recoleccion.
    public function ordenesRecolecciones()
    {
        return $this->hasMany(OrdenRecoleccion::class, 'id_ruta_recoleccion');
    }
}
