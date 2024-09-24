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
        ]);
    }
}
