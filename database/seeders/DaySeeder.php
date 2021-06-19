<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('days')->insert([
            [
                'name' => 'Ahad',
            ],
            [
                'name' => 'Senin',
            ],
            [
                'name' => 'Selasa',
            ],
            [
                'name' => 'Rabu',
            ],
            [
                'name' => 'Kamis',
            ],
            [
                'name' => 'Jumat',
            ],
            [
                'name' => 'Sabtu',
            ],
        ]);
    }
}
