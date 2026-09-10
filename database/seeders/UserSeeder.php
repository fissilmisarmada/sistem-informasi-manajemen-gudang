<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'Admin', 'email' => 'admin@gmail.com', 'role' => 'admin', 'password' => 'admin123'],
            ['name' => 'Staff', 'email' => 'staff@gmail.com', 'role' => 'staff', 'password' => 'staff123'],
            ['name' => 'Pimpinan', 'email' => 'pimpinan@gmail.com', 'role' => 'pimpinan', 'password' => 'pimpinan123'],
        ] as $u) {
            User::updateOrCreate(['email' => $u['email']], [
                'name' => $u['name'],
                'role' => $u['role'],
                'password' => Hash::make($u['password']),
                'email_verified_at' => now(),
            ]);
        }
    }
}
