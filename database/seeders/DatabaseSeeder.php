<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            ['role_id' => 1, 'nama' => 'Admin'],
            ['role_id' => 2, 'nama' => 'Petugas'],
            ['role_id' => 3, 'nama' => 'Peminjam'],
        ]);
    }
}
