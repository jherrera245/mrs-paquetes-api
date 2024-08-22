<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anaquel extends Model
{
    use HasFactory;

    protected $table = 'anaqueles';

    protected $casts = [
        'id_pasillo' => 'int',
        'nombre' => 'string', 
        'capacidad' => 'int',
        'paquetes_actuales' => 'int',
        'estado' => 'int',
        'id_paquete' => 'int',
    ];

    protected $fillable = [
        'id_pasillo',
        'nombre',
        'capacidad',
        'paquetes_actuales',
        'estado',
        'id_paquete',
    ];

    /**
     * Relación con la tabla Pasillo
     */
    public function pasillo()
    {
        return $this->belongsTo(Pasillo::class, 'id_pasillo');
    }

    /**
     * Relación con la tabla Paquete
     */
    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'id_paquete');
    }

    /**
     * Relación con la tabla Transaccion
     */
    public function transacciones()
    {
        return $this->hasMany(Transaccion::class, 'id_anaquel');
    }

    /**
     * Scope para búsquedas generales con múltiples filtros
     */
    public function scopeSearch($query, $filters)
    {
        if (isset($filters['id_pasillo'])) {
            $query->where('id_pasillo', $filters['id_pasillo']);
        }

        if (isset($filters['nombre'])) {
            $query->where('nombre', 'LIKE', "%{$filters['nombre']}%");
        }

        if (isset($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        return $query;
    }
    /**
     * Scope para búsqueda por Capacidad
     */
    public function scopeSearchByCapacidad($query, $capacidad)
    {
        return $query->where('capacidad', $capacidad);
    }

    /**
     * Scope para búsqueda por Paquetes Actuales
     */
    public function scopeSearchByPaquetesActuales($query, $paquetes_actuales)
    {
        return $query->where('paquetes_actuales', $paquetes_actuales);
    }

    /**
     * Scope para búsqueda combinada por Pasillo y Nombre
     */
    public function scopeSearchByPasilloAndNombre($query, $id_pasillo, $nombre)
    {
        return $query->where('id_pasillo', $id_pasillo)
                    ->where('nombre', 'LIKE', "%{$nombre}%");
    }
}