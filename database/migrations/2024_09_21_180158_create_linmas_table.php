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
        Schema::create('linmas', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique();
            $table->string('nip')->nullable();
            $table->string('nama');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('alamat');
            $table->string('pendidikan');
            $table->date('tanggal_bergabung')->nullable();
            $table->enum('status', ['aktif', 'tidak aktif'])->default('aktif');
            $table->string('pangkat')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('masa_kerja')->nullable();
            $table->string('pendidikan_terakhir')->nullable();
            $table->string('gol')->nullable();
            $table->string('tmt')->nullable();
            $table->string('kontak')->nullable();
            $table->decimal('gaji_pokok', 10, 2)->default(0);
            
            // Login fields
            $table->boolean('can_login')->default(false);
            $table->string('password')->nullable();
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linmas');
    }
};