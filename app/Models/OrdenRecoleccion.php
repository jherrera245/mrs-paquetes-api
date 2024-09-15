<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenRecoleccion extends Model
{
    use HasFactory;

    protected $table = 'ordenes_recolecciones';

    protected $fillable = [
        'id_ruta_recoleccion',
        'codigo_unico_recoleccion',
        'id_orden',
        'prioridad',
		'id_departamento',
		'id_municipio',
		'id_direccion',
		'destino',
        'estado',
        'recoleccion_iniciada',
        'recoleccion_finalizada',
    ];

    public function rutaRecoleccion()
    {
        return $this->belongsTo(RutaRecoleccion::class, 'id_ruta_recoleccion');
    }

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'id_orden');
    }

    public function scopeOrdenesRecolecciones($query, $id_ruta_recoleccion)
    {
        return $query->where('id_ruta_recoleccion', $id_ruta_recoleccion);
    }

    public function scopeOrdenesRecoleccionesActivas($query, $id_ruta_recoleccion)
    {
        return $query->where('id_ruta_recoleccion', $id_ruta_recoleccion)->where('estado', 1);
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

}
