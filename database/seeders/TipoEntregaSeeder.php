<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TipoEntregaSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        DB::table('tipo_entrega')->insert([
            ['id' => 1, 'entrega' => 'Entrega Normal', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'entrega' => 'Entrega Express', 'created_at' => $now, 'updated_at' => $now],
           
        ]);
    }
}
