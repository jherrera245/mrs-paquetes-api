<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarcaVehiculo extends Model
{
    use HasFactory;

    protected $table = 'marcas';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'id_marca');
    }

    public static function search($filters)
    {
        $query = self::query();

        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                if ($key === 'nombre') {
                    $query->where('nombre', 'like', '%' . $value . '%');
                }
            }
        }

        return $query;
    }
}
