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
        'dui',
        'telefono',
        'id_tipo_persona',
        'es_contribuyente',
        'id_genero',
        'fecha_registro',
        'id_estado',
        'id_departamento',
        'id_municipio',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'nit',
        'nrc',
        'giro',
        'nombre_empresa',
        'direccion',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }

    public function setTelefonoAttribute($value)
    {
        $this->attributes['telefono'] = preg_match('/^\d{4}-\d{4}$/', $value) 
            ? $value 
            : preg_replace('/(\d{4})(\d{4})/', '$1-$2', $value);
    }

    // Mutador para formatear el DUI
    public function setDuiAttribute($value)
    {
        // Si el valor del DUI está vacío, simplemente asigna el valor sin aplicar formato
        if (empty($value)) {
            $this->attributes['dui'] = $value;
            return;
        }

        // Aplicar formato solo si el valor no está vacío
        $formattedDui = preg_match('/^\d{8}-\d$/', $value)
            ? $value
            : substr($value, 0, 8) . '-' . substr($value, 8, 1);

        $this->attributes['dui'] = $formattedDui;
    }

    // Mutador para formatear el NIT 
    public function setNitAttribute($value)
{
    if (is_null($value) || trim($value) === '') {
        $this->attributes['nit'] = null;
        return;
    }

    // Remover cualquier carácter no numérico
    $formattedValue = preg_replace('/[^0-9]/', '', $value);

    // Aplicar el formato 1234-123456-123-0
    $this->attributes['nit'] = substr($formattedValue, 0, 4) . '-' . 
                                substr($formattedValue, 4, 6) . '-' . 
                                substr($formattedValue, 10, 3) . '-' . 
                                substr($formattedValue, 13, 1);
}



    public function tipoPersona()
    {
        return $this->belongsTo(TipoPersona::class, 'id_tipo_persona');
    }

    public static function filter($filters)
    {
        $query = self::query();

        foreach ($filters as $field => $value) {
            if (in_array($field, ['nombre', 'apellido', 'nombre_comercial', 'telefono', 'dui', 'nit', 'nrc', 'fecha_registro'])) {
                $query->where($field, 'like', '%' . $value . '%');
            } elseif (in_array($field, ['id_tipo_persona', 'es_contribuyente', 'id_genero', 'id_estado', 'id_departamento', 'id_municipio'])) {
                $query->where($field, $value);
            }
        }

        return $query;
    }
}