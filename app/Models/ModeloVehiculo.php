<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModeloVehiculo extends Model
{
    use HasFactory;

    protected $table = 'modelos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'id_marca',
    ];

    public function marca()
    {
        return $this->belongsTo(MarcaVehiculo::class, 'id_marca');
    }

    public static function search($filters)
    {
        $query = self::query();

        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                if ($key === 'nombre') {
                    $query->where('nombre', 'like', '%' . $value . '%');
                } elseif ($key === 'id_marca') {
                    $query->where('id_marca', $value);
                }
            }
        }

        return $query;
    }
}
