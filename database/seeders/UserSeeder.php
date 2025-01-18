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
        // Buat user SuperAdmin
        $superAdmin = User::create([
            'name' => 'superAdmin',
            'email' => 'superAdmin@mail.com',
            'password' => Hash::make('superAdmin'),
        ]);

        // Assign role superadmin ke user SuperAdmin
        $superAdminRole = Role::where('name', 'superadmin')->first();
        if ($superAdminRole) {
            $superAdmin->roles()->attach($superAdminRole->id);
        }

        // Buat user lain dengan beberapa role
        $user = User::create([
            'name' => 'salman fauzi',
            'email' => 'salman@mail.com',
            'password' => Hash::make('password123'),
        ]);

        // Assign multiple roles ke user
        $roles = Role::whereIn('name', ['prepared', 'Check1'])->pluck('id');
        $user->roles()->sync($roles);
    }
}
