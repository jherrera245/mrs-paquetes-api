<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
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
        'id_municipio',
        'nit',
        'nrc',
        'giro',
        'nombre_empresa',
        'direccion',
	];

    public function setTelefonoAttribute($value)
    {
        // Formatear el teléfono para agregar un guion si no lo tiene
        if (!preg_match('/\d{4}-\d{4}/', $value)) {
            $this->attributes['telefono'] = preg_replace('/(\d{4})(\d{4})/', '$1-$2', $value);
        } else {
            $this->attributes['telefono'] = $value;
        }
    }

    public function setDuiAttribute($value)
    {
        // Formatear el DUI para agregar un guion antes del último dígito si no lo tiene
        if (!preg_match('/\d{8}-\d/', $value)) {
            $this->attributes['dui'] = substr($value, 0, 8) . '-' . substr($value, 8, 1);
        } else {
            $this->attributes['dui'] = $value;
        }
    }

    
    public function tipoPersona()
    {
        return $this->belongsTo(TipoPersona::class, 'id_tipo_persona');
    } 
}
