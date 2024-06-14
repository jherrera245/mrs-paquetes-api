<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoIncidencia extends Model
{
    use HasFactory;

    protected $table = 'tipo_incidencia';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    // Relación con las incidencias
    public function incidencias()
    {
        return $this->hasMany(Incidencia::class, 'id_tipo_incidencia');
    }
}

