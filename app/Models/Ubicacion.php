<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ubicacion extends Model
{
    use HasFactory;

    // Especificar el nombre de la tabla
    protected $table = 'ubicaciones';

    protected $fillable = [
        'nomenclatura',
        'id_bodega',
        'id_pasillo',
        // Eliminar 'ocupado' del fillable, ya que ahora se determina dinámicamente.
    ];

    // Relación con la tabla `bodegas`.
    public function bodega()
    {
        return $this->belongsTo(Bodegas::class, 'id_bodega');
    }

    // Relación con la tabla `pasillos`.
    public function pasillo()
    {
        return $this->belongsTo(Pasillo::class, 'id_pasillo');
    }

    // Relación con la tabla `paquetes` a través de `ubicaciones_paquetes`.
    public function paquetes()
    {
        return $this->hasMany(UbicacionPaquete::class, 'id_ubicacion');
    }

    // Método accesor para obtener el estado de "ocupado" de la ubicación
    public function getOcupadoAttribute()
    {
        // Verificar si hay paquetes relacionados en ubicaciones_paquetes
        return $this->paquetes()->exists() ? 'Ocupado' : 'Desocupado';
    }

    /**
     * Obtener los datos formateados con nombres en lugar de IDs.
     *
     * @return array
     */
    public function getFormattedData()
    {
        return [
            'id' => $this->id,
            'nomenclatura' => $this->nomenclatura,
            'bodega' => $this->bodega ? $this->bodega->nombre : 'N/A',
            'id_bodega' => $this->bodega ? $this->bodega->id : 'N/A',
            'pasillo' => $this->pasillo ? $this->pasillo->nombre : 'N/A',
            'id_pasillo' => $this->pasillo ? $this->pasillo->id : 'N/A',
            'ocupado' => $this->ocupado, 
        ];
    }
}
