<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class clientes extends Model
{
    use HasFactory;
    protected $table = 'clientes';

	protected $fillable = [
        'nombre',
		'apellido',
        'nombre_comercial',
        'email',
        'telefono',
        'id_tipo_persona',
        'es_contribuyente',
        'id_genero',
        'dui',
       'fecha_registro',
        'id_estado',
        'id_departamento',
        'id_municipio'
	];
    public function tipo_persona()
    {
        return $this->belongsTo(tipo_persona::class, 'id_tipo_persona');
    } 
}
