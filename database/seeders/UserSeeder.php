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


        $superAdminRole = Role::whereIn('name', ['superadmin','Check1','Check2'])->pluck('id');
        $superAdmin->roles()->sync($superAdminRole);

        // // Buat user lain dengan beberapa role
        // $user = User::create([
        //     'name' => 'salman fauzi',
        //     'email' => 'salman@mail.com',
        //     'password' => Hash::make('password123'),
        // ]);

        // // Assign multiple roles ke user
        // $roles = Role::whereIn('name', ['prepared', 'Check1'])->pluck('id');
        // $user->roles()->sync($roles);
    }
}
