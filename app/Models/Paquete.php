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

}
