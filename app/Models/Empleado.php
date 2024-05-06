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
    }

