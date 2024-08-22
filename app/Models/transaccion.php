<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transaccion extends Model
{
    use HasFactory;

    protected $table = 'transacciones';

    protected $casts = [
        'id_paquete' => 'int',
        'id_bodega' => 'int',
        'id_pasillo' => 'int',
        'id_anaquel' => 'int',
        'tipoMovimiento' => 'string',
        'fecha' => 'date',
        'estado' => 'int',
    ];

    protected $fillable = [
        'id_paquete',
        'id_bodega',
        'id_pasillo',
        'id_anaquel',
        'tipoMovimiento',
        'fecha',
        'estado',
    ];

    /**
     * Relación con la tabla Paquete
     */
    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'id_paquete');
    }

    /**
     * Relación con la tabla Bodega
     */
    public function bodega()
    {
        return $this->belongsTo(Bodegas::class, 'id_bodega');
    }

    /**
     * Relación con la tabla Pasillo
     */
    public function pasillo()
    {
        return $this->belongsTo(Pasillo::class, 'id_pasillo');
    }

    /**
     * Relación con la tabla Anaquel
     */
    public function anaquel()
    {
        return $this->belongsTo(Anaquel::class, 'id_anaquel');
    }

    /**
     * Scope para búsquedas generales con múltiples filtros
     */
    public function scopeSearch($query, $filters)
    {
        if (isset($filters['id_paquete'])) {
            $query->where('id_paquete', $filters['id_paquete']);
        }
        else if (isset($filters['id_bodega'])) {
            $query->where('id_bodega', $filters['id_bodega']);
        }
        else if (isset($filters['id_pasillo'])) {
            $query->where('id_pasillo', $filters['id_pasillo']);
        }
        else if (isset($filters['id_anaquel'])) {
            $query->where('id_anaquel', $filters['id_anaquel']);
        }
        else if (isset($filters['tipoMovimiento'])) {
            $query->where('tipoMovimiento', 'LIKE', "%{$filters['tipoMovimiento']}%");
        }
        else if (isset($filters['fecha'])) {
            $query->where('fecha', $filters['fecha']);
        }
        else if (isset($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }
    }
}
