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
        Schema::create('tbl_users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('email')->unique();
            $table->string('password');
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('id_departement');
            $table->datetime('last_login')->nullable();
            $table->timestamps();

            // fk
            $table->foreign('id_departement')->references('id')->on('tbl_departement');
            $table->foreign('role_id')->references('id')->on('tbl_roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_users');
    }
};
