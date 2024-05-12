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
        'descripcion',
        'total_pago'
    ];

    public function orden():BelongsTo{
        return $this->belongsTo(Orden::class);
    }
    
    // public function paquete():BelongsTo{
    //     return $this->belongsTo(Paquete::class);
    // }
    
}
