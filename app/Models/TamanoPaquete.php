<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TamanoPaquete extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'tamano_paquete';

    /**
     * Los atributos que se pueden asignar de forma masiva.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
    ];

    /**
     * Obtener las tarifas_destinos asociadas con el tamaño de paquete.
     */
    public function tarifasDestinos()
    {
        return $this->hasMany(TarifasDestinos::class, 'id_tamano_paquete');
    }

    // relación con paquetes.
    public function paquetes()
    {
        return $this->hasMany(Paquete::class, 'id_tamano_paquete');
    }
}
