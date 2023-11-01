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
        Schema::create('thong_tin_nguoi_nhans', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_hoa_don')->length(20);
            $table->string('ten_nguoi_nhan');
            $table->string('so_dien_thoai');
            $table->string('dia_chi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thong_tin_nguoi_nhans');
    }
};
