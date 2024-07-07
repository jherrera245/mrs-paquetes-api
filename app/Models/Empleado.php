<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'email',
        'fecha_nacimiento',
        'fecha_contratacion',
        'salario',
        'id_estado',
        'id_cargo',
        'id_departamento',
        'id_municipio'
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

        return $query->with(['genero', 'estado', 'cargo', 'departamento', 'municipio'])->get();
    }
}
