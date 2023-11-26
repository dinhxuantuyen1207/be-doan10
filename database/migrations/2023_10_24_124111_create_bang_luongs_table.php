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
        Schema::create('bang_luongs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_nhan_vien')->length(20);
            $table->string('thang_nam', 7);
            $table->longText('cham_cong')->nullable();
            $table->decimal('he_so', 10, 2)->nullable();
            $table->decimal('thuong', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bang_luongs');
    }
};
