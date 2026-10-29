<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        abort_if(app()->environment('production'), 403, 'Seeder tidak boleh dijalankan di production.');

        foreach ([
            ['name' => 'Administrator', 'email' => 'admin@gmail.com', 'legacy_email' => 'admin_utbjm@gmail.com', 'role' => 'admin', 'password' => 'admin123'],
            ['name' => 'Staff', 'email' => 'staff@gmail.com', 'role' => 'staff', 'password' => 'staff123'],
            ['name' => 'Pimpinan', 'email' => 'pimpinan@gmail.com', 'role' => 'pimpinan', 'password' => 'pimpinan123'],
        ] as $u) {
            $user = User::where('email', $u['email'])
                ->when(isset($u['legacy_email']), fn ($query) => $query->orWhere('email', $u['legacy_email']))
                ->first() ?? new User;

            $user->fill([
                'name' => $u['name'],
                'email' => $u['email'],
                'role' => $u['role'],
                'password' => $u['password'],
                'email_verified_at' => now(),
            ])->save();
        }
    }
}
