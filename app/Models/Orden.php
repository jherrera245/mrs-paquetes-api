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
        'id_estado_paquetes',
        'costo_adicional',
        'concepto',
        'finished',
        'estado_pago',
    ];

    public function cliente(): BelongsTo{
        return $this->belongsTo(Clientes::class, 'id_cliente');
    }
    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'id_paquete');
    }  
    // Relación con el modelo TipoPago
        public function tipoPago()
        {
            return $this->belongsTo(TipoPago::class, 'id_tipo_pago');
        }

    // relacion con kardex.
    public function kardex()
    {
        return $this->hasMany(Kardex::class, 'id_orden');
    }
    
    public function direccion(): BelongsTo
    {
        return $this->belongsTo(Direcciones::class, 'id_direccion');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleOrden::class, 'id_orden');
    }
    public function detalleOrden()
    {
        return $this->hasMany(DetalleOrden::class, 'id_orden');
    }

    public function estadoPaquete()
    {
        return $this->hasMany(EstadoPaquete::class, 'id');
    }

    public function scopeSearch($query, $filters)
    {
        if (isset($filters['id_cliente'])) {
            $query->where('id_cliente', $filters['id_cliente']);
        }

        if (isset($filters['estado_pago'])) {
            $query->where('estado_pago', $filters['estado_pago']);
        }

        if (isset($filters['fecha_inicio']) && isset($filters['fecha_fin'])) {
            $query->whereBetween('created_at', [$filters['fecha_inicio'], $filters['fecha_fin']]);
        }

        return $query;
    }
}
