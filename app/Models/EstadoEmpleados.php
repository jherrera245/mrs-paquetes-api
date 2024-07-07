<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoEmpleados extends Model
{
    use HasFactory;

    protected $table = 'estado_empleados';

    protected $fillable = [
        'estado'
    ];

    public function empleados()
    {
        return $this->hasMany(Empleado::class, 'id_estado');
    }
}
