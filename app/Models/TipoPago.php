<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoPago extends Model
{
    use HasFactory;

    protected $table = 'tipo_pago';

    protected $fillable = [
        'pago'
    ];

    public function ordenes(): HasMany
    {
        return $this->hasMany(Orden::class, 'id');
    }
}
