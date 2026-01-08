<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Cek dulu biar gak error kalau dijalankan berulang
        if (!User::where('email', 'admin@rbeverything.com')->exists()) {
            User::create([
                'name' => 'RB Owner',
                'email' => 'owner@rbeverything.com',
                'password' => Hash::make('P@ssw0rd!!'),
                'email_verified_at' => now(),
            ]);
        }
    }
}