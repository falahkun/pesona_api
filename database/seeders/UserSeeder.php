<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id' => Uuid::uuid6()->toString(),
            'name' => 'admin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('adminpesona@123'),
            'phone_number' => '',
            'role' => 'admin',
            'active' => true,
        ]);
    }
}
