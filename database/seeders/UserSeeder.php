<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Departement;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'superAdmin',
            'email' => 'superAdmin@mail.com',
            'password' => Hash::make('superAdmin'),
            'role_id' => Role::where('name', 'superadmin')->first()->id,
            'id_departement' => Departement::where('nama_departement', 'ALL')->first()->id,
        ]);

    }
}
