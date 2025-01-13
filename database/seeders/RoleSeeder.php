<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $roles = [
            'superadmin',
            'prepared',
            'Check1',
            'Check2',
            'approvalManager',
            'viewer',
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}
