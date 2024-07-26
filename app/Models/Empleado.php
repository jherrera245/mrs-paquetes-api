<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Role;


class Empleado extends Model
{
    use HasFactory;

    protected $table = 'empleados';

    protected $fillable = [
        'nombres',
        'apellidos',
        'id_genero',
        'dui',
        'telefono',
        'fecha_nacimiento',
        'fecha_contratacion',
        'id_estado',
        'id_cargo',
        'id_departamento',
        'id_municipio',
        'created_by',
        'updated_by'
    ];

    public function genero()
    {
        return $this->belongsTo(Genero::class, 'id_genero');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoEmpleados::class, 'id_estado');
    }

    public function cargo()
    {
        return $this->belongsTo(Cargos::class, 'id_cargo');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'id_municipio');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }



    protected static function boot()
    {
        parent::boot();

        // Evento para asignar 'created_by' al crear un registro
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->id();
            }
        });

        // Evento para asignar 'updated_by' al actualizar un registro
        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }

    public static function search($filters)
    {
        $query = self::query();

        if (!empty($filters['nombres'])) {
            $query->where('nombres', 'like', '%' . $filters['nombres'] . '%');
        }

        if (!empty($filters['apellidos'])) {
            $query->where('apellidos', 'like', '%' . $filters['apellidos'] . '%');
        }

        if (!empty($filters['fecha_contratacion_inicio']) && !empty($filters['fecha_contratacion_fin'])) {
            $query->whereBetween('fecha_contratacion', [$filters['fecha_contratacion_inicio'], $filters['fecha_contratacion_fin']]);
        }

        if (!empty($filters['id_estado'])) {
            $query->where('id_estado', $filters['id_estado']);
        }

        return $query->with(['genero', 'estado', 'cargo', 'departamento', 'municipio'],)->get();
    }

}
