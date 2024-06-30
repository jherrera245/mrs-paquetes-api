<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleOrden extends Model
{
    use HasFactory;

    protected $table = 'detalle_orden';
    
    protected $fillable = [
        'id_orden',
        'id_paquete',
        'id_tipo_entrega',
        'id_estado_paquetes',
        'id_cliente_entrega',
        'id_direccion_entrega',
        'validacion_entrega',
        'instrucciones_entrega',
        'descripcion',
        'precio',
        'fecha_ingreso',
        'fecha_entrega',
    ];

    public function orden():BelongsTo {
        return $this->belongsTo(Orden::class);
    }
    
    public function paquete():BelongsTo {
        return $this->belongsTo(Paquete::class);
    }

    public function estado_paquete():BelongsTo {
        return $this->belongsTo(EstadoPaquetes::class);
    }

    public function cliente():BelongsTo {
        return $this->belongsTo(Clientes::class);
    }
    
    public function direcciones():BelongsTo
    {
        return $this->belongsTo(Direcciones::class);
    }
    
}
