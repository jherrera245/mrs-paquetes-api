<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TipoPagoSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        DB::table('tipo_pago')->insert([
            ['id' => 1, 'pago' => 'Efectivo', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'pago' => 'Tarjeta', 'created_at' => $now, 'updated_at' => $now],
            
            
        ]);
    }
}
