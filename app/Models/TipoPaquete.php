<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPaquete extends Model
{
    use HasFactory;

    protected $table = 'tipo_paquete';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];
}
