<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    protected $table = 'inventario';

    protected $fillable = [
        'id_paquete',
        'numero_ingreso',
        'cantidad',
        'fecha_entrada',
        'fecha_salida',
        'estado',
    ];

    protected $casts = [
        'fecha_entrada' => 'date',
        'fecha_salida' => 'date',
    ];

    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'id_paquete');
    }

    public function scopeByPaquete($query, $paqueteId)
    {
        return $query->where('id_paquete', $paqueteId);
    }

    public function scopeByNumeroIngreso($query, $numeroIngreso)
    {
        return $query->where('numero_ingreso', $numeroIngreso);
    }

    public function scopeByFechaEntrada($query, $fechaEntrada)
    {
        return $query->where('fecha_entrada', $fechaEntrada);
    }

    public function scopeByFechaSalida($query, $fechaSalida)
    {
        return $query->where('fecha_salida', $fechaSalida);
    }

    public function scopeByEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopeByCantidad($query, $cantidad)
    {
        return $query->where('cantidad', $cantidad);
    }
}
