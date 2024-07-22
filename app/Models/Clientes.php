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
        'created_by', 
        'updated_by'
	];

    protected static function boot()
    {
        parent::boot();

        // Evento para asignar 'created_by' al crear un registro
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->id();
            }
        });

        // Evento para asignar 'updated_by' al actualizar un registro
        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }

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

    public static function filter($filters)
    {
        $query = self::query();

        
        if (isset($filters['nombre'])) {
            $query->where('nombre', 'like', '%' . $filters['nombre'] . '%');
        }

        if (isset($filters['apellido'])) {
            $query->where('apellido', 'like', '%' . $filters['apellido'] . '%');
        }

        if (isset($filters['nombre_comercial'])) {
            $query->where('nombre_comercial', 'like', '%' . $filters['nombre_comercial'] . '%');
        }

        
        if (isset($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }

        
        if (isset($filters['telefono'])) {
            $query->where('telefono', 'like', '%' . $filters['telefono'] . '%');
        }

        if (isset($filters['id_tipo_persona'])) {
            $query->where('id_tipo_persona', 'like', '%' . $filters['id_tipo_persona'] . '%');
        }

        if (isset($filters['es_contribuyente'])) {
            $query->where('es_contribuyente', 'like', '%' . $filters['es_contribuyente'] . '%');
        }

        if (isset($filters['id_genero'])) {
            $query->where('id_genero', 'like', '%' . $filters['id_genero'] . '%');
        }

        if (isset($filters['dui'])) {
            $query->where('dui', 'like', '%' . $filters['dui'] . '%');
        }
        if (isset($filters['nit'])) {
            $query->where('nit', 'like', '%' . $filters['nit'] . '%');
        }

        if (isset($filters['nrc'])) {
            $query->where('nrc', 'like', '%' . $filters['nrc'] . '%');
        }

        if (isset($filters['fecha_registro'])) {
            $query->where('fecha_registro', 'like', '%' . $filters['fecha_registro'] . '%');
        }

        if (isset($filters['id_estado'])) {
            $query->where('id_estado', 'like', '%' . $filters['id_estado'] . '%');
        }

        if (isset($filters['id_departamento'])) {
            $query->where('id_departamento', 'like', '%' . $filters['id_departamento'] . '%');
        }

        if (isset($filters['id_municipio'])) {
            $query->where('id_municipio', 'like', '%' . $filters['id_municipio'] . '%');
        }


    
        return $query;
    }
}