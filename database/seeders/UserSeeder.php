<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@absensinuris.sch.id'],
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'guru@absensinuris.sch.id'],
            [
                'name' => 'Guru Pengampu',
                'username' => 'guru',
                'password' => Hash::make('password123'),
                'role' => 'guru',
            ]
        );
    }
}