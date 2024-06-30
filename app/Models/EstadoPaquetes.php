<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoPaquetes extends Model
{
    use HasFactory;

    protected $table = 'estado_paquetes';

    protected $fillable = [
        'nombre',
        'descripcion'
    ];
}
