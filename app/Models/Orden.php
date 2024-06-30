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
        'id_cliente',
        'id_direccion',
        'id_tipo_pago',
        'total_pagar',
        'costo_adicional',
        'concepto',
        'finished',
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

    public function direcciones():BelongsTo
    {
        return $this->belongsTo(Direcciones::class);
    }

}
