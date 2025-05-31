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
        Schema::create('input_b_d_s', function (Blueprint $table) {
            $table->id();
            $table->date('period');
            $table->integer('direct_competition');
            $table->integer('substitute_competition');
            $table->integer('indirect_competition');
            $table->integer('rating')->nullable();;
            $table->string('comment_input')->nullable();
            $table->string('comment_review')->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['Sedang Direview', 'Butuh Revisi', 'Selesai']);
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
        Schema::dropIfExists('input_b_d_s');
    }
};
