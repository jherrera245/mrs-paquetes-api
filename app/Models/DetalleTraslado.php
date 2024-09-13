<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleTraslado extends Model
{
    use HasFactory;

    protected $table = 'detalle_traslado';

    protected $fillable = [
        'id_traslado',
        'id_paquete',
        'estado'
    ];

    // Relación con el traslado
    public function traslado()
    {
        return $this->belongsTo(Traslado::class, 'id_traslado');
    }

    // Relación con el paquete
    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'id_paquete');
    }

    /**
     * Scope para filtrar detalles de traslado activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }

    /**
     * Scope para filtrar por traslado
     */
    public function scopePorTraslado($query, $idTraslado)
    {
        return $query->where('id_traslado', $idTraslado);
    }

    /**
     * Obtener los datos formateados de un detalle de traslado
     */
    public function getFormattedData()
    {
        return [
            'id' => $this->id,
            'id_traslado' => $this->id_traslado,
            'numero_traslado' => $this->traslado ? $this->traslado->numero_traslado : 'N/A',
            'paquete' => $this->paquete ? $this->paquete->descripcion_contenido : 'N/A',
            'uuid_paquete' => $this->paquete ? $this->paquete->uuid : 'N/A',
            'estado' => $this->estado ? 'Activo' : 'Inactivo',
            'fecha_traslado' => $this->traslado ? $this->traslado->fecha_traslado : 'N/A',
        ];
    }
}
