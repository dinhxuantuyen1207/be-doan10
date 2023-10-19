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
        Schema::create('trang_thai_hoa_dons', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_hoa_don')->length(20);
            $table->bigInteger('id_trang_thai')->length(20);
            $table->bigInteger('id_nhan_vien')->length(20)->nullable();
            $table->date('ngay_cap_nhap')->nullable();
            $table->text('ghi_chu')->nullable;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trang_thai_hoa_dons');
    }
};
