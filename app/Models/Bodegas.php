<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bodegas extends Model
{
    protected $table = 'bodegas';

	protected $casts = [
		'id_departamento' => 'int',
		'id_municipio' => 'int'
	];

	protected $fillable = [
		'nombre',
		'id_departamento',
		'id_municipio',
		'direccion'
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
		return $this->hasMany(Ruta::class, 'id_bodega');
	}

	public static function buscarConFiltros($nombre = null, $id_departamento = null, $id_municipio = null)
    {
        $query = self::query();

        if ($nombre) {
            $query->where('nombre', 'like', "%$nombre%");
        }

        if ($id_departamento) {
            $query->where('id_departamento', $id_departamento);
        }

        if ($id_municipio) {
            $query->where('id_municipio', $id_municipio);
        }

        return $query;
    }

}
