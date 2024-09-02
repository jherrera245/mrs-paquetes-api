<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UbicacionPaquete extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_paquete',
        'id_ubicacion',
        'estado',
    ];

    /**
     * Relación con la tabla `paquetes`.
     */
    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'id_paquete');
    }

    /**
     * Relación con la tabla `ubicaciones`.
     */
    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class, 'id_ubicacion');
    }
}
