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
        //
          // Tabel pivot many-to-many antara users dan kategori
          Schema::create('kategori_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('kategori_id');

            // Definisi foreign key
            $table->foreign('user_id')->references('id')->on('tbl_users')->onDelete('cascade');
            $table->foreign('kategori_id')->references('id')->on('tbl_kategori')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('kategori_user');
    }
};
