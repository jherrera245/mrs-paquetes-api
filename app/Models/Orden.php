<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Orden extends Model
{
    use HasFactory;

    protected $table = 'ordenes';
    protected $fillable = [
        'id_cliente_entrega',
        'telefono_entrega',
        'id_cliente_recible',
        'id_direccion',
        'id_tipo_entrega',
        'id_estado_paquetes',
        'id_paquete',
        'precio',
        'id_tipo_pago',
        'validacion_entrega',
        'costo_adicional',
        'instrucciones_entrega',
        'fecha_ingreso',
        'fecha_entrega'
    ];

    public function tipo_pago(): BelongsTo{
        return $this->belongsTo(TipoPago::class);
    }

    public function tipo_entrega():BelongsTo{
        return $this->belongsTo(TipoEntrega::class);
    }

    public function cliente():BelongsTo
    {
        return $this->belongsTo(Clientes::class);
    }

    public function detalle_orden(){
        return $this->hasMany(DetalleOrden::class);
    }

    // public function direccion():BelongsTo
    // {
    //     return $this->belongsTo(Direcciones::class);
    // }

    // public function estado_paquete():BelongsTo
    // {
    //     return $this->belongsTo(EstadoPaquete::class);
    // }

    // public function paquete():BelongsTo{
    //     return $this->belongsTo(Paquete::class);
    // }

}
