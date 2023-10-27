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
        Schema::create('thong_bao_nhan_viens', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_nhan_vien')->length(20);
            $table->string('thong_bao');
            $table->date('ngay_thong_ba');
            $table->integer('trang_thai_thong_bao');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thong_bao_nhan_viens');
    }
};
