<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Paquete extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_tipo_paquete',
        'id_empaque',
        'peso',
        'uuid',
        'tag',
        'id_estado_paquete',
        'fecha_envio',
        'fecha_entrega_estimada',
        'descripcion_contenido',
        'eliminado_at',
    ];

    protected $dates = ['eliminado_at'];

    protected static function booted()
    {
        static::addGlobalScope('not_eliminado', function (Builder $builder) {
            $builder->whereNull('eliminado_at');
        });
    }

    // Método para obtener paquetes incluyendo los eliminados
    public static function withEliminados()
    {
        return static::withoutGlobalScope('not_eliminado');
    }

    public function tipoPaquete()
    {
        return $this->belongsTo(TipoPaquete::class, 'id_tipo_paquete');
    }

    // Relación con la tabla de transaccion.
    public function transacciones()
    {
        return $this->hasMany(Transaccion::class, 'id_paquete');
    }

    // relacion con anaquel.
    public function anaquel()
    {
        return $this->hasMany(Anaquel::class, 'id_paquete');
    }

    public function empaquetado()
    {
        return $this->belongsTo(Empaquetado::class, 'id_empaque');
    }

    public function cliente()
{
    return $this->belongsTo(Clientes::class, 'id_cliente');
}

    public function estado()
    {
        return $this->belongsTo(EstadoPaquete::class, 'id_estado_paquete');
    }

    public static function search($filters)
    {
        $query = self::query();

        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                switch ($key) {
                    case 'tipo_paquete':
                        $query->where(function ($q) use ($value) {
                            $q->whereHas('tipoPaquete', function ($subq) use ($value) {
                                $subq->where('nombre', 'like', '%' . $value . '%');
                            })->orWhere('id_tipo_paquete', $value); // Buscar por ID directamente
                        });
                        break;
                    case 'empaque':
                        $query->where(function ($q) use ($value) {
                            $q->whereHas('empaquetado', function ($subq) use ($value) {
                                $subq->where('nombre', 'like', '%' . $value . '%');
                            })->orWhere('id_empaque', $value); // Buscar por ID directamente
                        });
                        break;
                    case 'estado_paquete':
                        $query->where(function ($q) use ($value) {
                            $q->whereHas('estado', function ($subq) use ($value) {
                                $subq->where('nombre', 'like', '%' . $value . '%');
                            })->orWhere('id_estado_paquete', $value); // Buscar por ID directamente
                        });
                        break;
                    case 'descripcion_contenido':
                        $query->where('descripcion_contenido', 'like', '%' . $value . '%');
                        break;
                    case 'peso':
                        $query->where('peso', $value);
                        break;
                    case 'fecha_envio_desde':
                        $query->whereDate('fecha_envio', '>=', $value);
                        break;
                    case 'fecha_envio_hasta':
                        $query->whereDate('fecha_envio', '<=', $value);
                        break;
                    case 'fecha_entrega_estimada_desde':
                        $query->whereDate('fecha_entrega_estimada', '>=', $value);
                        break;
                    case 'fecha_entrega_estimada_hasta':
                        $query->whereDate('fecha_entrega_estimada', '<=', $value);
                        break;
                    case 'palabra_clave':
                        $query->where(function ($q) use ($value) {
                            $q->where('descripcion_contenido', 'like', '%' . $value . '%')
                            ->orWhere('uuid', 'like', '%' . $value . '%')
                            ->orWhere('tag', 'like', '%' . $value . '%')
                            ->orWhereHas('tipoPaquete', function ($subq) use ($value) {
                                $subq->where('nombre', 'like', '%' . $value . '%');
                            })
                                ->orWhereHas('empaquetado', function ($subq) use ($value) {
                                    $subq->where('empaquetado', 'like', '%' . $value . '%');
                                })
                                ->orWhereHas('estado', function ($subq) use ($value) {
                                    $subq->where('nombre', 'like', '%' . $value . '%');
                                });
                        });
                        break;
                    default:
                        break;
                }
            }
        }

        return $query->with(['tipoPaquete', 'empaquetado', 'estado']);
    }

}
