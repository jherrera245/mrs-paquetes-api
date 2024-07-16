<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoVehiculo extends Model
{
    use HasFactory;

    protected $table = 'estado_vehiculos';

    protected $fillable = [
        'estado',
    ];

    public $timestamps = true;
}
