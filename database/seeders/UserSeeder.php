<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Doni Darmawan',
            'email' => 'doni@localhost',
            'password' => bcrypt('rahasia'),
            'token' => 'secret',
        ]);

        User::create([
            'name' => 'Eko Khannedy',
            'email' => 'eko@localhost',
            'password' => bcrypt('rahasia'),
            'token' => 'secret',
        ]);
    }
}
