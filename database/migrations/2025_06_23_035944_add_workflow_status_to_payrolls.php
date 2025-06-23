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
        Schema::table('payrolls', function (Blueprint $table) {
            $table->enum('processing_status', [
                'draft',       // Data awal, belum diverifikasi
                'verified',    // Data sudah diverifikasi
                'calculated',  // Perhitungan gaji selesai
                'approved',    // Disetujui untuk dibayar
                'processed',   // Proses pembayaran
                'completed',   // Selesai dibayar
                'rejected'     // Ditolak
            ])->default('draft')->after('payment_status');
            $table->text('status_notes')->nullable()->after('processing_status');
            $table->unsignedBigInteger('verified_by')->nullable()->after('status_notes');
            $table->unsignedBigInteger('approved_by')->nullable()->after('verified_by');
            $table->timestamp('verified_at')->nullable()->after('approved_by');
            $table->timestamp('approved_at')->nullable()->after('verified_at');

            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'processing_status',
                'status_notes',
                'verified_by',
                'approved_by',
                'verified_at',
                'approved_at'
            ]);
        });
    }
};
