<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Bodega;


class Ubicacion extends Model
{
    use HasFactory;

    // Especificar el nombre de la tabla
    protected $table = 'ubicaciones';

    protected $fillable = [
        'nomenclatura',
        'id_bodega',
    ];

    // Relación con la tabla `bodegas`.
    public function bodega()
    {
        return $this->belongsTo(Bodega::class, 'id_bodega');
    }

    /**
     * Relación con la tabla `paquetes` a través de `ubicaciones_paquetes`.
     */
    public function paquetes()
    {
        return $this->hasMany(UbicacionPaquete::class, 'id_ubicacion');
    }
}
