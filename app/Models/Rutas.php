<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rutas extends Model
{
    protected $table = 'rutas';

	protected $casts = [
		'id_destino' => 'int',
		'id_bodega' => 'int',
		'id_estado' => 'int',
		'distancia_km' => 'float',
		'duracion_aproximada' => 'float',
		'fecha_programada' => 'datetime'
	];

	protected $fillable = [
		'id_destino',
		'nombre',
		'id_bodega',
		'id_estado',
		'distancia_km',
		'duracion_aproximada',
		'fecha_programada'
	];

	public function destino()
	{
		return $this->belongsTo(Destino::class, 'id_destino');
	}

	public function bodega()
	{
		return $this->belongsTo(Bodega::class, 'id_bodega');
	}

	public function estado_ruta()
	{
		return $this->belongsTo(EstadoRuta::class, 'id_estado');
	}

	public function asignacion_rutas()
	{
		return $this->hasMany(AsignacionRuta::class, 'id_ruta');
	}
}
