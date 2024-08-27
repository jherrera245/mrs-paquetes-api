<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    use HasFactory;

    protected $table = 'kardex';

    protected $fillable = [
        'id_paquete',
        'id_orden',
        'cantidad',
        'numero_ingreso',
        'tipo_movimiento',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'date',
        'tipo_movimiento' => 'string',
    ];

    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'id_paquete');
    }

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'id_orden');
    }

    public function scopeByTipoMovimiento($query, $tipoMovimiento)
    {
        return $query->where('tipo_movimiento', $tipoMovimiento);
    }

    public function scopeByFecha($query, $fecha)
    {
        return $query->where('fecha', $fecha);
    }

    public function scopeByPaquete($query, $paqueteId)
    {
        return $query->where('id_paquete', $paqueteId);
    }

    public function scopeByOrden($query, $ordenId)
    {
        return $query->where('id_orden', $ordenId);
    }

    public function scopeByNumeroIngreso($query, $numeroIngreso)
    {
        return $query->where('numero_ingreso', $numeroIngreso);
    }
}
