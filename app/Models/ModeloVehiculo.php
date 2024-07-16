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
    ];

    /**
     * Search for modelos based on given filters.
     *
     * @param  array  $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
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
