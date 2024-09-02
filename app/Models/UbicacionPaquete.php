<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UbicacionPaquete extends Model
{
    use HasFactory;

    protected $table = 'ubicaciones_paquetes'; // Cambia el nombre a 'ubicaciones_paquetes'

    protected $fillable = [
        'id_paquete',
        'id_ubicacion',
        'estado',
    ];

    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'id_paquete');
    }

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class, 'id_ubicacion');
    }
}
