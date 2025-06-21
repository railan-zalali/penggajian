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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('linmas_id')->constrained('linmas');
            $table->dateTime('waktu');
            $table->string('status'); // C/Masuk, C/Keluar
            $table->string('status_baru')->nullable(); // Lembur Masuk, Lembur Keluar
            $table->string('pengecualian')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
