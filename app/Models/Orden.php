<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Orden extends Model
{
    use HasFactory;

    protected $table = 'ordenes';

    protected $fillable = [
        'id_cliente',
        'id_direccion',
        'id_tipo_pago',
        'total_pagar',
        'costo_adicional',
        'concepto',
        'finished',
    ];

    public function cliente(): BelongsTo{
        return $this->belongsTo(Clientes::class, 'id_cliente');
    }

     
    // Relación con el modelo TipoPago
        public function tipoPago()
        {
            return $this->belongsTo(TipoPago::class, 'id_tipo_pago');
        }


    public function direccion(): BelongsTo
    {
        return $this->belongsTo(Direcciones::class, 'id_direccion');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleOrden::class, 'id_orden');
    }
}
