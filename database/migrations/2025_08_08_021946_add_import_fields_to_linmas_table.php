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
            $table->string('nip')->nullable()->after('nik');
            $table->string('pangkat')->nullable()->after('posisi');
            $table->string('jabatan')->nullable()->after('pangkat');
            $table->string('masa_kerja')->nullable()->after('jabatan');
            $table->string('pendidikan_terakhir')->nullable()->after('masa_kerja');
            $table->string('gol')->nullable()->after('pendidikan_terakhir');
            $table->string('tmt')->nullable()->after('gol');
            $table->string('thn')->nullable()->after('tmt');
            $table->string('bln')->nullable()->after('thn');
            $table->string('kontak')->nullable()->after('bln');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('linmas', function (Blueprint $table) {
            $table->dropColumn(['nip', 'pangkat', 'jabatan', 'masa_kerja', 'pendidikan_terakhir', 'gol', 'tmt', 'thn', 'bln', 'kontak']);
        });
    }
};
