<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifasDestinos extends Model
{
    protected $table = 'tarifas_destinos';

    /**
     * Los atributos que se pueden asignar de forma masiva.
     *
     * @var array
     */
    protected $fillable = [
        'id_tarifa',
        'id_tamano_paquete',
        'id_departamento',
        'id_municipio',
        'monto',
    ];


    /**
     * Obtener la tarifa asociada con el destino.
     */
    public function tarifa()
    {
        return $this->belongsTo(Tarifa::class, 'id_tarifa');
    }

    /**
     * Obtener el tamaño del paquete asociado con el destino.
     */
    public function tamanoPaquete()
    {
        return $this->belongsTo(TamanoPaquete::class, 'id_tamano_paquete');
    }

    /**
     * Obtener el departamento asociado con el destino.
     */
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }

    /**
     * Obtener el municipio asociado con el destino.
     */
    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'id_municipio');
    }
}
