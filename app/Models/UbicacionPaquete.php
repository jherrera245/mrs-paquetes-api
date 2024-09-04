<?php

namespace App\Models; // Este es el namespace correcto

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UbicacionPaquete extends Model
{
    use HasFactory;

    protected $table = 'ubicaciones_paquetes'; // Nombre de la tabla

    protected $fillable = [
        'id_paquete',
        'id_ubicacion',
        'estado',
    ];

    // Relación con el modelo Paquete
    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'id_paquete');
    }

    // Relación con el modelo Ubicacion
    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class, 'id_ubicacion');
    }

    /**
     * Obtener los datos formateados con nombres y IDs.
     *
     * @return array
     */
    public function getFormattedData()
    {
        return [
            'id' => $this->id,
            'paquete' => $this->paquete ? $this->paquete->descripcion_contenido : 'N/A',  // Mostrar 'descripcion_contenido' de paquetes
            'id_paquete' => $this->paquete ? $this->paquete->id : 'N/A',
            'ubicacion' => $this->ubicacion ? $this->ubicacion->nomenclatura : 'N/A',
            'id_ubicacion' => $this->ubicacion ? $this->ubicacion->id : 'N/A',
            'estado' => $this->estado,
        ];
    }
}
