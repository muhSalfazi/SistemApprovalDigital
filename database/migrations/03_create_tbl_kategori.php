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
        Schema::create('tbl_kategori', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori', 50);
            $table->string('alias_name', 4);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_kategori', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
