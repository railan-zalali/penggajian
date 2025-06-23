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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'perangkat_desa'])->default('admin');
            $table->unsignedBigInteger('linmas_id')->nullable();
            $table->foreign('linmas_id')->references('id')->on('linmas')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['linmas_id']);
            $table->dropColumn(['role', 'linmas_id']);
        });
    }
};
