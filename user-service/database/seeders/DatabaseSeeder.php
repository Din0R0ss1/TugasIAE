<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Budi Santoso', 'email' => 'budi@gmail.com', 'password' => Hash::make('password')],
            ['name' => 'Andi Wijaya', 'email' => 'andi@gmail.com', 'password' => Hash::make('password')],
            ['name' => 'Siti Rahayu', 'email' => 'siti@gmail.com', 'password' => Hash::make('password')],
            ['name' => 'Rina Kartika', 'email' => 'rina@gmail.com', 'password' => Hash::make('password')],
            ['name' => 'Agus Salim', 'email' => 'agus@gmail.com', 'password' => Hash::make('password')],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
