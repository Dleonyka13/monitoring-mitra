<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data User untuk masing-masing role
        $users = [
            [
                'name'     => 'Administrator System',
                'email'    => 'admin@example.com',
                'role'     => 'admin',
            ],
            [
                'name'     => 'Kepala Bagian',
                'email'    => 'kepala@example.com',
                'role'     => 'kepala',
            ],
            [
                'name'     => 'Pegawai Staff',
                'email'    => 'pegawai@example.com',
                'role'     => 'pegawai',
            ],
            [
                'name'     => 'Mitra Kerja',
                'email'    => 'mitra@example.com',
                'role'     => 'mitra',
            ],
        ];

        foreach ($users as $userData) {
            User::create([
                'id'                => Str::uuid(), // Generasi UUID manual
                'name'              => $userData['name'],
                'email'             => $userData['email'],
                'password'          => Hash::make('password123'), // Password default sama semua
                'role'              => $userData['role'],
                'email_verified_at' => now(),
                'remember_token'    => Str::random(10),
            ]);
        }
    }
}