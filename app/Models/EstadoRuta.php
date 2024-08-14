<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoRuta extends Model
{
    use HasFactory;

    protected $table = 'estado_rutas';

    protected $fillable = [
        'estado',
    ];

    //relacionamos con la tabla rutas.
    public function asignacion_rutas()
	{
		return $this->hasMany(AsignacionRutas::class, 'id_estado');
	}
}
