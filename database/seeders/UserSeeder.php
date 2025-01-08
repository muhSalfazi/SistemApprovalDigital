<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua role berdasarkan nama
        $roles = Role::whereIn('name', ['superadmin', 'admin', 'prepared', 'approver'])->get();

        // Data user untuk setiap role
        $users = [
            [
                'name' => 'Super Admin User',
                'email' => 'superadmin@mail.com',
                'password' => Hash::make('password123'),
                'role' => 'superadmin',
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@mail.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ],
            [
                'name' => 'Prepared User',
                'email' => 'prepared@mail.com',
                'password' => Hash::make('password123'),
                'role' => 'prepared',
            ],
            [
                'name' => 'Approver User',
                'email' => 'approver@mail.com',
                'password' => Hash::make('password123'),
                'role' => 'approver',
            ],
        ];

        // Loop untuk membuat user berdasarkan role
        foreach ($users as $userData) {
            $role = $roles->firstWhere('name', $userData['role']);

            if ($role) {
                User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => $userData['password'],
                    'role_id' => $role->id,
                ]);
            }
        }
    }
}
