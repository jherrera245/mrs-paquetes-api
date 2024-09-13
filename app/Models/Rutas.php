<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rutas extends Model
{
	use HasFactory;

	protected $table = 'rutas';

	protected $casts = [
		'id_bodega' => 'int',
		'estado' => 'int',
		'fecha_programada' => 'datetime'
	];

	protected $fillable = [
		'nombre',
		'tipo',
		'id_bodega',
		'estado',
		'fecha_programada'
	];

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

	// relacion con ruta recoleccion.
	public function rutasRecolecciones()
	{
		return $this->hasMany(RutaRecoleccion::class, 'id_ruta');
	}

	public static function search($filters)
	{
		$query = self::query();

		foreach ($filters as $key => $value) {
			if (!empty($value)) {
				if ($key === 'id_bodega' || $key === 'estado') {
					$query->where($key, $value);
				} elseif ($key === 'nombre') {
					$query->where('nombre', 'like', '%' . $value . '%');
				} elseif ($key === 'tipo') {
					$query->where($key, $value);
				} elseif ($key === 'fecha_programada') {
					$query->whereDate('fecha_programada', $value);
				}
			}
		}

		return $query->with(['bodega', 'estado_ruta']);
	}
}
