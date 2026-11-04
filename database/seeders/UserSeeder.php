<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@absensinuris.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);
    
        User::create([
            'name' => 'Guru Pengampu',
            'username' => 'guru',
            'email' => 'guru@absensinuris.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'guru',
        ]);
    }
}
