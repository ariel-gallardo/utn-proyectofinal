<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')::table('rols')->insert([
            'denominacion' => 'administrador',
        ]);
        DB::table('users')::table('rols')->insert([
            'denominacion' => 'cocinero',
        ]);
        DB::table('users')::table('rols')->insert([
            'denominacion' => 'cajero',
        ]);
        DB::table('users')::table('rols')->insert([
            'denominacion' => 'delivery',
        ]);
        DB::table('users')::table('rols')->insert([
            'denominacion' => 'cliente',
        ]);
    }
}
