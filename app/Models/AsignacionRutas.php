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
		return $this->belongsTo(EstadoRuta::class, 'id');
	}

	public function ruta()
	{
		return $this->belongsTo(Rutas::class, 'id_ruta');
	}

	public function vehiculo()
	{
		return $this->belongsTo(Vehiculo::class, 'id_vehiculo');
	}

	public function paquete()
	{
		return $this->belongsTo(Paquete::class, 'id_paquete');
	}

	public static function filtrar($filtros)
    {
        $query = self::query();

        
        if (isset($filtros['codigo_unico_asignacion'])) {
            $query->where('codigo_unico_asignacion', 'like', '%' . $filtros['codigo_unico_asignacion'] . '%');
        }

        if (isset($filtros['id_ruta'])) {
            $query->where('id_ruta', $filtros['id_ruta']);
        }

        if (isset($filtros['id_vehiculo'])) {
            $query->where('id_vehiculo', $filtros['id_vehiculo']);
        }

        if (isset($filtros['id_paquete'])) {
            $query->where('id_paquete', $filtros['id_paquete']);
        }

        if (isset($filtros['fecha'])) {
            $query->whereDate('fecha', $filtros['fecha']);
        }

        if (isset($filtros['id_estado'])) {
            $query->where('id_estado', $filtros['id_estado']);
        }

        return $query->get();
    }
}
