<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recoleccion extends Model
{
    use HasFactory;

    protected $table = 'recolecciones';

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'id_vehiculo',
        'codigo_barra',
        'fecha_recoleccion',
        'estado_paquete',
    ];

    // Casts para transformar los datos al recuperarlos o guardarlos
    protected $casts = [
        'fecha_recoleccion' => 'date',
        'estado_paquete' => 'string', // Para manejar el enum como string
    ];

    /**
     * Relación con el modelo Vehiculo
     * Un registro de recolección pertenece a un vehículo
     */
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'id_vehiculo');
    }

    /**
     * Relación con el modelo Paquete (opcional, si decides agregar la relación directa)
     * Un registro de recolección puede estar relacionado con un paquete específico
     */
    // public function paquete()
    // {
    //     return $this->belongsTo(Paquete::class, 'codigo_barra', 'codigo_barra'); // Relaciona con el código de barra del paquete
    // }

    /**
     * Scope para filtrar por estado del paquete
     */
    public function scopeByEstado($query, $estado)
    {
        return $query->where('estado_paquete', $estado);
    }

    /**
     * Scope para filtrar por vehículo
     */
    public function scopeByVehiculo($query, $vehiculoId)
    {
        return $query->where('id_vehiculo', $vehiculoId);
    }

    /**
     * Scope para filtrar por fecha de recolección
     */
    public function scopeByFechaRecoleccion($query, $fecha)
    {
        return $query->whereDate('fecha_recoleccion', $fecha);
    }
}
