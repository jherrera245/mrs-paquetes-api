<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoPaquete extends Model
{
    use HasFactory;

    protected $table = 'estado_paquetes';

    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    public function paquete()
    {
        return $this->hasMany(Paquete::class, 'id_estado_paquete');
    }
    public function orden()
    {
        return $this->hasMany(Paquete::class, 'id');
    }


}
