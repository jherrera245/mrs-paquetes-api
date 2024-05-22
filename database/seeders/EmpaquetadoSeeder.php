<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmpaquetadoSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        DB::table('empaquetado')->insert([
            ['empaquetado' => 'Caja de cartón', 'created_at' => $now, 'updated_at' => $now],
            ['empaquetado' => 'Bolsa de plástico', 'created_at' => $now, 'updated_at' => $now],
            ['empaquetado' => 'Sobres acolchados', 'created_at' => $now, 'updated_at' => $now],
            ['empaquetado' => 'Papel burbuja', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
