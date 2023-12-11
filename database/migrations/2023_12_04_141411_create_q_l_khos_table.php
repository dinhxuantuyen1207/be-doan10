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
        Schema::create('q_l_khos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_san_pham')->length(20);
            $table->integer('so_luong_nhap')->length(20);
            $table->integer('so_luong_da_ban')->length(20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('q_l_khos');
    }
};
