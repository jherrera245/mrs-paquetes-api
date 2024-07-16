<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPersona extends Model
{
    use HasFactory;
    protected $table = 'tipo_persona';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

public static function filtrarPorNombre($filtroNombre = null)
    {
        $query = self::query();

        if ($filtroNombre) {
            $query->where('nombre', 'like', '%' . $filtroNombre . '%');
        }

        return $query;
    }

}