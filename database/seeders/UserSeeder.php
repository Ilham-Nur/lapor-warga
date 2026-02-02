<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@laporwarga.test',
                'role' => 'super_admin',
            ],
            [
                'name' => 'Admin',
                'email' => 'admin2@laporwarga.test',
                'role' => 'admin',
            ],
            [
                'name' => 'Moderator',
                'email' => 'moderator@laporwarga.test',
                'role' => 'moderator',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']], // key unik
                [
                    'name'      => $user['name'],
                    'role'      => $user['role'],
                    'password'  => Hash::make('password123'),
                    'is_active' => true,
                ]
            );
        }
    }
}
