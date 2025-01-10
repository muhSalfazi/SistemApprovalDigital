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
            $table->unsignedBigInteger('id_departement');
            $table->unsignedBigInteger('id_kategori');
            $table->unsignedBigInteger('id_user');
            $table->string('title');
            $table->string('no_transaksi');
            $table->string('remark');
            $table->string('lampiran_pdf');

            // foreign key
            $table->foreign('id_departement')->references('id')->on('tbl_departement')->onDelete('cascade');
            $table->foreign('id_kategori')->references('id')->on('tbl_kategori')->onDelete('cascade');
            $table->foreign('id_user')->references('id')->on('tbl_users')->onDelete('cascade');

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
