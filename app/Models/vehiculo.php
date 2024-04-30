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
        'id_estado',
        'id_marca',
        'id_modelo',
        'year_fabricacion',
    ];
/*
public function empleadoConductor()
{
return $this->belongsTo(Empleado::class, 'id_empleado_conductor');
}

public function empleadoApoyo()
{
return $this->belongsTo(Empleado::class, 'id_empleado_apoyo');
}

public function estado()
{
return $this->belongsTo(Estado::class, 'id_estado');
}
 */
    public function marca()
    {
        return $this->belongsTo(MarcaVehiculo::class, 'id_marca');
    }

    public function modelo()
    {
        return $this->belongsTo(ModeloVehiculo::class, 'id_modelo');
    }
}
