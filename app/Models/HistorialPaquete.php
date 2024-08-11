<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialPaquete extends Model
{
    use HasFactory;

    protected $table = 'historial_de_paquetes';

    protected $fillable = [
        'id_paquete',
        'fecha_hora',
        'id_usuario',
        'accion'
    ];

    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'id_paquete');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
