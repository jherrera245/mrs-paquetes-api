<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destinos extends Model
{
    protected $table = 'destinos';

	protected $casts = [
		'id_departamento' => 'int',
		'id_municipio' => 'int',
	];

	protected $fillable = [
		'nombre',
		'descripcion',
		'id_departamento',
		'id_municipio',
		'estado'
	];

	public function departamento()
	{
		return $this->belongsTo(Departamento::class, 'id_departamento');
	}

	public function municipio()
	{
		return $this->belongsTo(Municipio::class, 'id_municipio');
	}

	public function rutas()
	{
		return $this->hasMany(Ruta::class, 'id_destino');
	}

	public static function filter(array $filters)
    {
        $query = self::query();

        if (isset($filters['id_departamento'])) {
            $query->where('id_departamento', $filters['id_departamento']);
        }

        if (isset($filters['id_municipio'])) {
            $query->where('id_municipio', $filters['id_municipio']);
        }

        return $query;
    }
}
