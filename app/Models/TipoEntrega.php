<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEntrega extends Model
{
    use HasFactory;

    protected $table = 'tipo_entrega';
    protected $fillable = [
        'entrega'
    ];

    public function ordenes(){
        return $this->hasMany(Orden::class);
    }
}
