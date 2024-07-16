<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Incidencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_paquete',
        'fecha_hora',
        'id_tipo_incidencia',
        'descripcion',
        'estado',
        'fecha_resolucion',
        'id_usuario_reporta',
        'id_usuario_asignado',
        'solucion',
    ];

    // Relación con el tipo de incidencia
    public function tipoIncidencia()
    {
        return $this->belongsTo(TipoIncidencia::class, 'id_tipo_incidencia');
    }

    // Relación con el paquete
    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'id_paquete');
    }

    // Relación con el usuario que reporta la incidencia
    public function usuarioReporta()
    {
        return $this->belongsTo(User::class, 'id_usuario_reporta');
    }

    // Relación con el usuario asignado a la incidencia
    public function usuarioAsignado()
    {
        return $this->belongsTo(User::class, 'id_usuario_asignado');
    }

    public function scopeSearch(Builder $query, array $filters)
    {
        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                switch ($key) {
                    case 'tipo_incidencia':
                        if (is_numeric($value)) {
                            $query->where('id_tipo_incidencia', $value);
                        } else {
                            $query->whereHas('tipoIncidencia', function ($q) use ($value) {
                                $q->where('nombre', 'like', '%' . $value . '%');
                            });
                        }
                        break;
                    case 'paquete':
                        $query->whereHas('paquete', function ($q) use ($value) {
                            $q->where('descripcion_contenido', 'like', '%' . $value . '%');
                        });
                        break;
                    case 'usuario_reporta':
                        $query->whereHas('usuarioReporta', function ($q) use ($value) {
                            $q->where('name', 'like', '%' . $value . '%');
                        });
                        break;
                    case 'usuario_asignado':
                        $query->whereHas('usuarioAsignado', function ($q) use ($value) {
                            $q->where('name', 'like', '%' . $value . '%');
                        });
                        break;
                    case 'estado':
                        if (is_numeric($value)) {
                            $query->where('estado', $value);
                        } else {
                            // Assuming 'estado' is a string column for textual states
                            $query->where('estado', 'like', '%' . $value . '%');
                        }
                        break;
                    case 'fecha_hora':
                        $query->whereDate('fecha_hora', $value);
                        break;
                    case 'palabra_clave':
                        $query->where(function ($q) use ($value) {
                            $q->where('descripcion', 'like', '%' . $value . '%')
                                ->orWhere('solucion', 'like', '%' . $value . '%')
                                ->orWhereHas('tipoIncidencia', function ($q) use ($value) {
                                    $q->where('nombre', 'like', '%' . $value . '%');
                                })
                                ->orWhereHas('paquete', function ($q) use ($value) {
                                    $q->where('descripcion_contenido', 'like', '%' . $value . '%');
                                })
                                ->orWhereHas('usuarioReporta', function ($q) use ($value) {
                                    $q->where('name', 'like', '%' . $value . '%');
                                })
                                ->orWhereHas('usuarioAsignado', function ($q) use ($value) {
                                    $q->where('name', 'like', '%' . $value . '%');
                                });
                        });
                        break;
                }
            }
        }

        return $query;
    }
}
