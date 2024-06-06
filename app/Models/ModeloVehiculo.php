<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModeloVehiculo extends Model
{
    use HasFactory;

    protected $table = 'modelos';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

}
