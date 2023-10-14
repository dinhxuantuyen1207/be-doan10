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
        Schema::create('danh_gia_san_phams', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_san_pham')->length(20);
            $table->decimal('sao_danh_gia', 3, 1);
            $table->text('binh_luan_danh_gia');
            $table->bigInteger('id_nguoi_dung')->length(20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danh_gia_san_phams');
    }
};
