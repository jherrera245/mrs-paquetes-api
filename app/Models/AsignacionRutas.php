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
		'id_departamento' => 'int',
		'id_municipio' => 'int',
		'id_direccion' => 'int',
		'prioridad' => 'int',
		'fecha' => 'datetime',
		'id_estado' => 'int'
	];

	protected $fillable = [
		'codigo_unico_asignacion',
		'id_ruta',
		'id_vehiculo',
		'id_paquete',
		'prioridad',
		'id_departamento',
		'id_municipio',
		'id_direccion',
		'destino',
		'fecha',
		'id_estado',
		'status',
	];

	public function estado_ruta()
	{
		return $this->belongsTo(EstadoRuta::class, 'id');
	}

	public function ruta()
	{
		return $this->belongsTo(Rutas::class, 'id_ruta');
	}

	public function departamento()
	{
		return $this->belongsTo(Departamento::class, 'id_deparatamento');
	}

	public function municipio()
	{
		return $this->belongsTo(Municipio::class, 'id_municipio');
	}

	public function direccion()
	{
		return $this->belongsTo(Direcciones::class, 'id_direccion');
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

		if (isset($filtros['destino'])) {
            $query->where('codigo_unico_asignacion', 'like', '%' . $filtros['codigo_unico_asignacion'] . '%');
        }

		
		if (isset($filtros['id_deparatamento'])) {
            $query->where('id_deparatamento', $filtros['id_deparatamento']);
        }

        if (isset($filtros['id_municipio'])) {
            $query->where('id_municipio', $filtros['id_municipio']);
        }

		if (isset($filtros['id_direccion'])) {
            $query->where('id_direccion', $filtros['id_direccion']);
        }

		if (isset($filtros['prioridad'])) {
            $query->where('prioridad', $filtros['prioridad']);
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

		$query->whereHas('ruta', function ($q) {
			$q->where('estado', '=', 1); 
		});

		$query->where('status', 1);

        return $query;
    }
}
