<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'username' => 'budi',
            'password' => Hash::make('budi123'),
            'nama_lengkap' => 'Budi Santoso',
        ]);

        User::create([
            'username' => 'siti',
            'password' => Hash::make('siti123'),
            'nama_lengkap' => 'Siti Aminah',
        ]);
    }
}
