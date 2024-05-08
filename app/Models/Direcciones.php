<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direcciones extends Model
{
    protected $table = 'direcciones';

	protected $casts = [
		'id_cliente' => 'int',
		'id_departamento' => 'int',
		'id_municipio' => 'int'
	];

	protected $fillable = [
		'id_cliente',
		'nombre_contacto',
		'telefono',
		'id_departamento',
		'id_municipio',
		'direccion',
		'referencia'
	];

	public function cliente()
	{
		return $this->belongsTo(Cliente::class, 'id_cliente');
	}

	public function departamento()
	{
		return $this->belongsTo(Departamento::class, 'id_departamento');
	}

	public function municipio()
	{
		return $this->belongsTo(Municipio::class, 'id_municipio');
	}

	public function ordenes()
	{
		return $this->hasMany(Ordene::class, 'id_direccion');
	}
}
