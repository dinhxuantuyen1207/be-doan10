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
        Schema::create('khos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_san_pham')->length(20);
            $table->bigInteger('id_nhan_vien')->length(20);
            $table->integer('so_luong_nhap');
            $table->decimal('gia_nhap', 10, 2);
            $table->date('ngay_nhap');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khos');
    }
};
