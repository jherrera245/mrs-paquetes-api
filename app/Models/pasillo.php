<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pasillo extends Model
{
    use HasFactory;

    protected $table = 'pasillos';

    protected $casts = [
        'id_bodega' => 'int',
        'nombre' => 'string',
        'capacidad' => 'int',
        'estado' => 'int',
    ];

    protected $fillable = [
        'id_bodega',
        'nombre',
        'capacidad',
        'estado',
    ];

    /**
     * Relación con la tabla Bodega
     */
    public function bodega()
    {
        return $this->belongsTo(Bodegas::class, 'id_bodega');
    }

    /**
     * Relación con la tabla Anaquel
     */
    public function anaqueles()
    {
        return $this->hasMany(Anaquel::class, 'id_pasillo');
    }

    /**
     * Relacion con la tabla transacciones
    */
    public function transacciones()
    {
        return $this->hasMany(Transaccion::class, 'id_pasillo');
    }

    /**
     * Scope para búsquedas generales con múltiples filtros
     */
    public function scopeSearch($query, $filters)
    {
        if (isset($filters['id_bodega'])) {
            $query->where('id_bodega', $filters['id_bodega']);
        }

        if (isset($filters['nombre'])) {
            $query->where('nombre', 'LIKE', "%{$filters['nombre']}%");
        }

        if (isset($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }
    }
    
}
