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
            // Hapus field overtime_payment yang tidak dipakai
            $table->dropColumn('overtime_payment');
        });
        
        // Hapus payroll details dengan type overtime
        DB::table('payroll_details')->where('type', 'overtime')->delete();
        
        // Update enum type di payroll_details untuk menghapus overtime
        DB::statement("ALTER TABLE payroll_details MODIFY COLUMN type ENUM('allowance', 'deduction', 'base')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Kembalikan field overtime_payment
            $table->decimal('overtime_payment', 15, 2)->nullable()->after('base_salary');
        });
        
        // Kembalikan enum type dengan overtime
        DB::statement("ALTER TABLE payroll_details MODIFY COLUMN type ENUM('allowance', 'deduction', 'base', 'overtime')");
    }
};