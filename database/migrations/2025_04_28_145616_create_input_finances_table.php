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
        Schema::create('input_finances', function (Blueprint $table) {
            $table->id();
            $table->date('period');
            $table->integer('penjualan');
            $table->integer('pendapatan_lain');
            $table->integer('total_pendapatan');
            $table->integer('total_hpp');
            $table->integer('laba_kotor');
            $table->integer('biaya_operasional');
            $table->integer('laba_sebelum_pajak');
            $table->integer('laba_bersih');
            $table->integer('gross_profit_margin');
            $table->integer('net_profit_margin');
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
        Schema::dropIfExists('input_finances');
    }
};
