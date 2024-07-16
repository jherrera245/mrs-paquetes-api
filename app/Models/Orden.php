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
        'fecha_entrega',
    ];

    public function clienteEntrega()
    {
        return $this->belongsTo(Clientes::class, 'id_cliente_entrega');
    }

    public function clienteRecible()
    {
        return $this->belongsTo(Clientes::class, 'id_cliente_recible');
    }

    public function direccion()
    {
        return $this->belongsTo(Direcciones::class, 'id_direccion');
    }

    public function tipoEntrega()
    {
        return $this->belongsTo(TipoEntrega::class, 'id_tipo_entrega');
    }

    public function estadoPaquetes()
    {
        return $this->belongsTo(EstadoPaquete::class, 'id_estado_paquetes');
    }

    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'id_paquete');
    }

    public function tipoPago()
    {
        return $this->belongsTo(TipoPago::class, 'id_tipo_pago');
    }

    public function scopeSearch(Builder $query, array $filters)
    {
        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                switch ($key) {
                    case 'id_cliente_entrega':
                    case 'id_cliente_recible':
                    case 'id_direccion':
                    case 'id_tipo_entrega':
                    case 'id_estado_paquetes':
                    case 'id_paquete':
                    case 'id_tipo_pago':
                        $query->where($key, $value);
                        break;
                    case 'telefono_entrega':
                    case 'validacion_entrega':
                    case 'instrucciones_entrega':
                        $query->where($key, 'like', '%' . $value . '%');
                        break;
                    case 'precio':
                    case 'costo_adicional':
                        $query->where($key, $value);
                        break;
                    case 'fecha_ingreso':
                    case 'fecha_entrega':
                        $query->whereDate($key, $value);
                        break;
                }
            }
        }

        return $query->with(['clienteEntrega', 'clienteRecible', 'direccion', 'tipoEntrega', 'estadoPaquetes', 'paquete', 'tipoPago']);
    }
}
