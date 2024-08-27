<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarifa extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'tarifas';

    /**
     * Los atributos que se pueden asignar de forma masiva.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
    ];

    /**
     * Obtener las tarifas_destinos asociadas con la tarifa.
     */
    public function tarifasDestinos()
    {
        return $this->hasMany(TarifasDestinos::class, 'id_tarifa');
    }
}
