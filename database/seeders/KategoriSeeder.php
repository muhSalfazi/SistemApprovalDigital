<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['nama_kategori' => 'Pengajuan Anggaran'],
            ['nama_kategori' => 'Permintaan Pembelian'],
            ['nama_kategori' => 'Permintaan Perjalanan Dinas'],
            ['nama_kategori' => 'Permintaan Pengadaan Barang/Jasa'],
            ['nama_kategori' => 'Persetujuan Kontrak'],
            ['nama_kategori' => 'Persetujuan Perubahan'],
            ['nama_kategori' => 'Persetujuan Proyek'],
            ['nama_kategori' => 'Persetujuan Vendor'],
        ];

        foreach ($categories as $category) {
            Kategori::create($category);
        }
    }
}
