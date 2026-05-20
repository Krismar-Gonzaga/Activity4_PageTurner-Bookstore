<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // -----------------------------------------------
        // Admin Account
        // -----------------------------------------------
        User::updateOrCreate(
            ['email' => 'admin@pageturner.com'],
            [
                'name'              => 'PageTurner Admin',
                'email'             => 'admin@pageturner.com',
                'password'          => Hash::make('Admin@1234'),
                'role'              => 'admin',
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]
        );

        
    }
}