<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models;

class DetalleOrden extends Model
{
    use HasFactory;

    protected $table = 'detalle_orden';

    protected $fillable = [
        'id',
        'id_orden',
        'id_paquete',
        'id_tipo_entrega',
        'id_estado_paquetes',
        'id_direccion_entrega',
        'validacion_entrega',
        'instrucciones_entrega',
        'descripcion',
        'precio',
        'fecha_ingreso',
        'fecha_entrega'
    ];

    public function orden(): BelongsTo
    {
        return $this->belongsTo(Orden::class, 'id');
    }

    public function paquete(): BelongsTo
    {
        return $this->belongsTo(Paquete::class, 'id');

    }

    public function asignacionRuta()
    {
        return $this->belongsTo(AsignacionRutas::class, 'id_asignacion_ruta');
    }

    public function tipoEntrega(): BelongsTo
    {
        return $this->belongsTo(TipoEntrega::class, 'id_tipo_entrega');
    }

    public function estadoEntrega(): BelongsTo
    {
        return $this->belongsTo(EstadoPaquete::class, 'id_estado_paquetes');
    }

    public function direccionEntrega(): BelongsTo
    {
        return $this->belongsTo(Direcciones::class, 'id_direccion_entrega');
    }

    public function departamentoEntrega(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'id');
    }

    public function municipioEntrega(): BelongsTo
    {
        return $this->belongsTo(Municipio::class, 'id');
    }

    public function tipoPaquete(): BelongsTo
    {
        return $this->belongsTo(TipoPaquete::class, 'id');
    }

    public function empaquetado(): BelongsTo
    {
        return $this->belongsTo(Empaquetado::class, 'id');
    }

    public static function filtrarDetalleOrden($filters)
    {
        $query = self::query();

        if (isset($filters['id_orden'])) {
            $query->where('id_orden', $filters['id_orden']);
        }

		if (isset($filters['id_paquete'])) {
            $query->where('id_paquete', $filters['id_paquete']);
        }

		if (isset($filters['id_tipo_entrega'])) {
            $query->where('id_tipo_entrega', $filters['id_tipo_entrega']);
        }

        if (isset($filters['id_estado_paquetes'])) {
            $query->where('id_estado_paquetes', $filters['id_estado_paquetes']);
        }

		if (isset($filters['id_direccion_entrega'])) {
            $query->where('id_direccion_entrega', $filters['id_direccion_entrega']);
        }

        return $query;
    }
}
