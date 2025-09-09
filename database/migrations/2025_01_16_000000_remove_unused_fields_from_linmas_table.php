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
            // Hapus field tahun dan bulan yang tidak dipakai
            $table->dropColumn(['thn', 'bln']);
            
            // Hapus field pekerjaan dan posisi yang sudah diganti dengan jabatan
            $table->dropColumn(['pekerjaan', 'posisi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('linmas', function (Blueprint $table) {
            // Kembalikan field yang dihapus
            $table->string('thn')->nullable()->after('tmt');
            $table->string('bln')->nullable()->after('thn');
            $table->string('pekerjaan')->nullable()->after('pendidikan');
            $table->string('posisi')->nullable()->after('jabatan');
        });
    }
};