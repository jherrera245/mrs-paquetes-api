<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genero extends Model
{
    use HasFactory;

    protected $table = 'genero';

    protected $fillable = [
        'nomnbre'
    ];

    public function empleados()
    {
        return $this->hasMany(Empleado::class, 'id_genero');
    }
}
