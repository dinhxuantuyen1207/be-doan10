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
        Schema::create('hoa_dons', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_nguoi_dung')->length(20);
            $table->date('ngay_mua');
            $table->decimal('gia_tien_thanh_toan', 10, 2);
            $table->decimal('tax', 10, 2)->nullable();
            $table->decimal('ship', 10, 2)->nullable();
            $table->string('trang_thai_thanh_toan');
            $table->date('ngay_thanh_toan')->nullable();
            $table->bigInteger('id_trang_thai')->length(20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoa_dons');
    }
};
