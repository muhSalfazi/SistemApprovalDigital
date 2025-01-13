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
        Schema::create('tbl_approval', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_submission');
            $table->unsignedBigInteger('auditor_id');
            $table->enum('status',['approved','rejected']);
            $table->string('remark')->nullable();
            $table->dateTime('approved_date');
            $table->timestamps();

            // foreign key
            $table->foreign('id_submission')->references('id')->on('tbl_submission')->onDelete('cascade');
            $table->foreign('auditor_id')->references('id')->on('tbl_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_approval');
    }
};
