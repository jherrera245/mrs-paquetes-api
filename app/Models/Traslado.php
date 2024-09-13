<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Traslado extends Model
{
    use HasFactory;

    protected $table = 'traslados';

    protected $fillable = [
        'bodega_origen',
        'bodega_destino',
        'numero_traslado',
        'fecha_traslado',
        'estado',
        'user_id'
    ];

    
    public function bodegaOrigen()
    {
        return $this->belongsTo(Bodegas::class, 'bodega_origen');
    }

    
    public function bodegaDestino()
    {
        return $this->belongsTo(Bodegas::class, 'bodega_destino');
    }

    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // relacion con detalle traslado.
    public function detalleTraslado()
    {
        return $this->hasMany(DetalleTraslado::class, 'id_traslado');
    }

    public function getFormattedData()
    {
        return [
            'id' => $this->id,
            'bodega_origen' => $this->bodegaOrigen ? $this->bodegaOrigen->nombre : 'N/A',
            'bodega_destino' => $this->bodegaDestino ? $this->bodegaDestino->nombre : 'N/A',
            'paquete' => $this->paquete ? $this->paquete->descripcion_contenido : 'N/A',
            'numero_traslado' => $this->numero_traslado,
            'fecha_traslado' => $this->fecha_traslado,
            'estado' => $this->estado,
            'user' => $this->user ? $this->user->name : 'N/A',
        ];
    }

}
