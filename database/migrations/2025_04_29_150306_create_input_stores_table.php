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
        Schema::create('input_stores', function (Blueprint $table) {
            $table->id();
            $table->date('period');
            $table->integer('aksesibilitas');
            $table->integer('visibilitas');
            $table->json('lingkungan')->nullable();
            $table->integer('lalu_lintas');
            $table->integer('kepadatan_kendaraan');
            $table->integer('parkir_mobil');
            $table->integer('parkir_motor');
            $table->integer('rating')->nullable();
            $table->string('comment_input')->nullable();
            $table->string('comment_review')->nullable();
            $table->enum('status', ['Sedang Direview Manager Area', 'Sedang Direview Manager BD', 'Butuh Revisi', 'Selesai']);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->unique(['store_id', 'period'], 'unique_user_store_period');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('input_stores');
    }
};
