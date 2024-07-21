<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
        return $this->belongsTo(Clientes::class);
    }

    public function tipo_pago(): BelongsTo{
        return $this->belongsTo(Clientes::class);
    }
    
    public function direcciones():BelongsTo{
        return $this->belongsTo(Direcciones::class);
    }
}
