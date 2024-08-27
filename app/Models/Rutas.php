<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rutas extends Model
{
	use HasFactory;

	protected $table = 'rutas';

	protected $casts = [
		'id_destino' => 'int',
		'id_bodega' => 'int',
		'estado' => 'int',
		'distancia_km' => 'float',
		'duracion_aproximada' => 'float',
		'fecha_programada' => 'datetime'
	];

	protected $fillable = [
		'id_destino',
		'nombre',
		'id_bodega',
		'estado',
		'distancia_km',
		'duracion_aproximada',
		'fecha_programada'
	];

	public function destino()
	{
		return $this->belongsTo(Destinos::class, 'id_destino');
	}

	public function bodega()
	{
		return $this->belongsTo(Bodegas::class, 'id_bodega');
	}

	public function estado_ruta()
	{
		return $this->belongsTo(EstadoRuta::class, 'estado');
	}

	public function asignacion_rutas()
	{
		return $this->hasMany(AsignacionRutas::class, 'id_ruta');
	}

	public static function search($filters)
	{
		$query = self::query();

		foreach ($filters as $key => $value) {
			if (!empty($value)) {
				if ($key === 'id_destino' || $key === 'id_bodega' || $key === 'estado') {
					$query->where($key, $value);
				} elseif ($key === 'nombre') {
					$query->where('nombre', 'like', '%' . $value . '%');
				} elseif ($key === 'distancia_km' || $key === 'duracion_aproximada') {
					$query->where($key, $value);
				} elseif ($key === 'fecha_programada') {
					$query->whereDate('fecha_programada', $value);
				}
			}
		}

		return $query->with(['destino', 'bodega', 'estado_ruta']);
	}
}
