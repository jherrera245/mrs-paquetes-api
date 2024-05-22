<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incidencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_paquete',
        'fecha_hora',
        'id_tipo_incidencia',
        'descripcion',
        'estado',
        'fecha_resolucion',
        'id_usuario_reporta',
        'id_usuario_asignado',
        'solucion',
    ];

    // Relación con el tipo de incidencia
    public function tipoIncidencia()
    {
        return $this->belongsTo(TipoIncidencia::class, 'id_tipo_incidencia');
    }

    // Relación con el paquete
    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'id_paquete');
    }

    // Relación con el usuario que reporta la incidencia
    public function usuarioReporta()
    {
        return $this->belongsTo(User::class, 'id_usuario_reporta');
    }

    // Relación con el usuario asignado a la incidencia
    public function usuarioAsignado()
    {
        return $this->belongsTo(User::class, 'id_usuario_asignado');
    }
}
