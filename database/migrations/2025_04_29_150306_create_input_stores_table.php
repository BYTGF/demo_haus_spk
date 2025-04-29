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
            $table->integer('aksesibilitas');
            $table->integer('visibilitas');
            $table->integer('lingkungan');
            $table->integer('lalu_lintas');
            $table->integer('area_parkir');
            $table->integer('rating');
            $table->string('comment_input')->nullable();
            $table->string('comment_review')->nullable();
            $table->enum('status', ['Sedang Direview Manager Area', 'Sedang Direview Manager BD', 'Butuh Revisi', 'Selesai']);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
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
