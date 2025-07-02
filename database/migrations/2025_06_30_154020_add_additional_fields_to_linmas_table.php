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
        Schema::table('linmas', function (Blueprint $table) {
            $table->date('tanggal_bergabung')->nullable()->after('pekerjaan');
            $table->enum('status', ['aktif', 'tidak aktif'])->default('aktif')->after('tanggal_bergabung');
            $table->string('posisi')->default('Perangkat Desa')->after('status');
            $table->decimal('gaji_pokok', 10, 2)->default(0)->after('posisi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('linmas', function (Blueprint $table) {
            $table->dropColumn(['tanggal_bergabung', 'status', 'posisi', 'gaji_pokok']);
        });
    }
};
