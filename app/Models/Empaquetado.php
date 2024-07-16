<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empaquetado extends Model
{
    use HasFactory;

    protected $table = 'empaquetado';

    protected $fillable = [
        'empaquetado',
    ];
}
