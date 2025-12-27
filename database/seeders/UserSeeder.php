<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'id' => Str::uuid(),
            'name' => 'Admin Sistem',
            'email' => 'admin@mail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Kepala
        User::create([
            'id' => Str::uuid(),
            'name' => 'Kepala BPS',
            'email' => 'kepala@mail.com',
            'password' => Hash::make('password'),
            'role' => 'kepala',
        ]);

        // Pegawai & Mitra (18 data)
        foreach (range(1, 18) as $i) {
            User::create([
                'id' => Str::uuid(),
                'name' => "User {$i}",
                'email' => "user{$i}@mail.com",
                'password' => Hash::make('password'),
                'role' => $i <= 8 ? 'pegawai' : 'mitra',
            ]);
        }
    }
}
