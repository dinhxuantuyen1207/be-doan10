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
        Schema::create('nhan_viens', function (Blueprint $table) {
            $table->id();
            $table->string('tai_khoan')->unique();
            $table->string('mat_khau');
            $table->string('ten_nhan_vien');
            $table->string('so_dien_thoai')->nullable();
            $table->bigInteger('id_chuc_vu')->length(20)->nullable();
            $table->string('anh_nhan_vien')->nullable();
            $table->json('anh_cccd')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nhan_viens');
    }
};
