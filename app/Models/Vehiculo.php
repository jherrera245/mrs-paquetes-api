<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'vehiculos';

    protected $fillable = [
        'id_empleado_conductor',
        'id_empleado_apoyo',
        'placa',
        'capacidad_carga',
        'id_bodega',
        'id_estado',
        'id_marca',
        'id_modelo',
        'year_fabricacion',
    ];

    public function conductor()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado_conductor');
    }

    public function apoyo()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado_apoyo');
    }

    // relacion con recoleccion.
    public function recolecciones()
    {
        return $this->hasMany(Recoleccion::class, 'id_vehiculo');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoVehiculo::class, 'id_estado');
    }
    
    public function bodega()
    {
        return $this->belongsTo(Bodegas::class, 'id_bodega');
    }
    
    public function marca()
    {
        return $this->belongsTo(MarcaVehiculo::class, 'id_marca');
    }

    public function modelo()
    {
        return $this->belongsTo(ModeloVehiculo::class, 'id_modelo');
    }

    public static function search($filters)
    {
        $query = self::query();

        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                switch ($key) {
                    case 'conductor':
                    case 'apoyo':
                        $query->whereHas($key, function ($q) use ($value) {
                            $q->where('nombres', 'like', '%' . $value . '%')
                            ->orWhere('apellidos', 'like', '%' . $value . '%');
                        });
                        break;
                    case 'estado':
                        if (is_numeric($value)) {
                            $query->where('id_estado', $value);
                        } else {
                            $query->whereHas('estado', function ($q) use ($value) {
                                $q->where('estado', 'like', '%' . $value . '%');
                            });
                        }
                        break;
                    case 'marca':
                        if (is_numeric($value)) {
                            $query->where('id_marca', $value);
                        } else {
                            $query->whereHas('marca', function ($q) use ($value) {
                                $q->where('nombre', 'like', '%' . $value . '%');
                            });
                        }
                        break;
                    case 'modelo':
                        if (is_numeric($value)) {
                            $query->where('id_modelo', $value);
                        } else {
                            $query->whereHas('modelo', function ($q) use ($value) {
                                $q->where('nombre', 'like', '%' . $value . '%');
                            });
                        }
                        break;
                    case 'placa':
                        $query->where('placa', 'like', '%' . $value . '%');
                        break;
                    case 'capacidad_carga':
                        $query->where('capacidad_carga', $value);
                        break;
                    case 'year_fabricacion':
                        $query->where('year_fabricacion', $value);
                        break;
                    case 'palabra_clave':
                        $query->where(function ($q) use ($value) {
                            $q->where('placa', 'like', '%' . $value . '%')
                            ->orWhereHas('conductor', function ($subq) use ($value) {
                                $subq->where('nombres', 'like', '%' . $value . '%')
                                ->orWhere('apellidos', 'like', '%' . $value . '%');
                            })
                                ->orWhereHas('apoyo', function ($subq) use ($value) {
                                    $subq->where('nombres', 'like', '%' . $value . '%')
                                    ->orWhere('apellidos', 'like', '%' . $value . '%');
                                })
                                ->orWhereHas('estado', function ($subq) use ($value) {
                                    $subq->where('estado', 'like', '%' . $value . '%');
                                })
                                ->orWhereHas('marca', function ($subq) use ($value) {
                                    $subq->where('nombre', 'like', '%' . $value . '%');
                                })
                                ->orWhereHas('modelo', function ($subq) use ($value) {
                                    $subq->where('nombre', 'like', '%' . $value . '%');
                                });
                        });
                        break;
                    default:
                        break;
                }
            }
        }

        return $query->with(['conductor', 'apoyo', 'estado', 'marca', 'modelo']);
    }
}