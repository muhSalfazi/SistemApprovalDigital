<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Departement;

class DepartementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data departemen
        $departments = [
            ['nama_departement' => 'HRGA'],
            ['nama_departement' => 'FAS'],
            ['nama_departement' => 'PPIC'],
            ['nama_departement' => 'ALL'],
        ];

        // Buat data departemen menggunakan Eloquent
        foreach ($departments as $department) {
            Departement::create($department);
        }
    }
}
