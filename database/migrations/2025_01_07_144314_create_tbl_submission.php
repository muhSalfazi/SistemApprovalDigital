<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_submission', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_devisi');
            $table->unsignedBigInteger('id_kategori');
            $table->unsignedBigInteger('id_prepared');
            $table->string('title');
            $table->string('no_transaksi');
            $table->string('remark');
            $table->string('lampiran_pdf');
            $table->datetime('tgl_pengajuan'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_submission');
    }
};