<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsignacionRutas extends Model
{
    protected $table = 'asignacion_rutas';

	protected $casts = [
		'id_ruta' => 'int',
		'id_vehiculo' => 'int',
		'id_paquete' => 'int',
		'fecha' => 'datetime',
		'id_estado' => 'int'
	];

	protected $fillable = [
		'codigo_unico_asignacion',
		'id_ruta',
		'id_vehiculo',
		'id_paquete',
		'fecha',
		'id_estado'
	];

	public function estado_ruta()
	{
		return $this->belongsTo(EstadoRuta::class, 'id_estado');
	}

	public function ruta()
	{
		return $this->belongsTo(Ruta::class, 'id_ruta');
	}

	public function vehiculo()
	{
		return $this->belongsTo(Vehiculo::class, 'id_vehiculo');
	}

	public function paquete()
	{
		return $this->belongsTo(Paquete::class, 'id_paquete');
	}
}
