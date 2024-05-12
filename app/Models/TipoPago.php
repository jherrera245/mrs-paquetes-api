<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TipoPago extends Model
{
    use HasFactory;

    protected $table = 'tipo_pago';

    protected $fillable = [
        'pago'
    ];

    public function ordenes(){
        return $this->hasMany(Orden::class);
    }
}
