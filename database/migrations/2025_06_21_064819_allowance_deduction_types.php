<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('allowance_deduction_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('type', ['allowance', 'deduction']);
            $table->enum('calculation_type', ['fixed', 'percentage']);
            $table->decimal('default_value', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_taxable')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default values
        DB::table('allowance_deduction_types')->insert([
            [
                'name' => 'Tunjangan Transportasi',
                'code' => 'TRANS',
                'type' => 'allowance',
                'calculation_type' => 'fixed',
                'default_value' => 0.00,
                'description' => 'Tunjangan transportasi harian',
                'is_taxable' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tunjangan Makan',
                'code' => 'MEAL',
                'type' => 'allowance',
                'calculation_type' => 'fixed',
                'default_value' => 0.00,
                'description' => 'Tunjangan makan harian',
                'is_taxable' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PPh 21',
                'code' => 'TAX',
                'type' => 'deduction',
                'calculation_type' => 'percentage',
                'default_value' => 0.00,
                'description' => 'Pajak Penghasilan Pasal 21',
                'is_taxable' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'BPJS Kesehatan',
                'code' => 'BPJS_KES',
                'type' => 'deduction',
                'calculation_type' => 'percentage',
                'default_value' => 0.00,
                'description' => 'Iuran BPJS Kesehatan',
                'is_taxable' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'BPJS Ketenagakerjaan',
                'code' => 'BPJS_TK',
                'type' => 'deduction',
                'calculation_type' => 'percentage',
                'default_value' => 0.00,
                'description' => 'Iuran BPJS Ketenagakerjaan',
                'is_taxable' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create table for individual assignments
        Schema::create('linmas_allowances_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('linmas_id')->constrained('linmas')->onDelete('cascade');
            $table->foreignId('type_id')->constrained('allowance_deduction_types')->onDelete('cascade');
            $table->decimal('value', 15, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Ensure each linmas only has one entry per type
            $table->unique(['linmas_id', 'type_id']);
        });

        // Create table for payroll details
        Schema::create('payroll_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained('payrolls')->onDelete('cascade');
            $table->foreignId('type_id')->nullable()->constrained('allowance_deduction_types')->onDelete('set null');
            $table->string('name');
            $table->enum('type', ['allowance', 'deduction', 'base', 'overtime']);
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_details');
        Schema::dropIfExists('linmas_allowances_deductions');
        Schema::dropIfExists('allowance_deduction_types');
    }
};
