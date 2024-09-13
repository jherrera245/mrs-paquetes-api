<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaqueteReporte extends Model
{
    use HasFactory;

    protected $table = 'paquete_reporte';

    protected $fillable = [
        'id_paquete',
        'id_orden',
        'id_cliente',
        'id_empleado_reporta',
        'descripcion_dano',
        'costo_reparacion',
        'estado',
    ];

    // Relación con la tabla 'paquete'
    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'id_paquete');
    }

    // Relación con la tabla 'orden'
    public function orden()
    {
        return $this->belongsTo(Orden::class, 'id_orden');
    }

    // Relación con la tabla 'cliente'
    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'id_cliente');
    }

    // Relación con la tabla 'empleado'
    public function empleadoReporta()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado_reporta');
    }
}
