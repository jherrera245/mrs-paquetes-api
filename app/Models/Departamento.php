<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamento';

	protected $fillable = [
		'nombre'
	];

	public function bodegas()
	{
		return $this->hasMany(Bodega::class, 'id_departamento');
	}

	public function clientes()
	{
		return $this->hasMany(Cliente::class, 'id_departamento');
	}

	public function destinos()
	{
		return $this->hasMany(Destino::class, 'id_departamento');
	}

	public function direcciones()
	{
		return $this->hasMany(Direccione::class, 'id_departamento');
	}

	public function empleados()
	{
		return $this->hasMany(Empleado::class, 'id_departamento');
	}

	public function municipios()
	{
		return $this->hasMany(Municipio::class, 'id_departamento');
	}
}
