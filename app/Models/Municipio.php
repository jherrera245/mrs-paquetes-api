<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $table = 'municipios';

	protected $casts = [
		'id_departamento' => 'int'
	];

	protected $fillable = [
		'nombre',
		'id_departamento'
	];

	public function departamento()
	{
		return $this->belongsTo(Departamento::class, 'id_departamento');
	}

	public function bodegas()
	{
		return $this->hasMany(Bodega::class, 'id_municipio');
	}

	public function clientes()
	{
		return $this->hasMany(Cliente::class, 'id_municipio');
	}

	public function destinos()
	{
		return $this->hasMany(Destino::class, 'id_municipio');
	}

	public function direcciones()
	{
		return $this->hasMany(Direccione::class, 'id_municipio');
	}

	public function empleados()
	{
		return $this->hasMany(Empleado::class, 'id_municipio');
	}
}
