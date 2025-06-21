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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('linmas_id')->constrained('linmas');
            $table->integer('total_days_present'); // Number of days present
            $table->decimal('base_salary', 15, 2); // Basic salary
            $table->decimal('overtime_payment', 15, 2)->nullable(); // Overtime payment
            $table->decimal('total_salary', 15, 2); // Total salary (base + overtime)
            $table->date('payroll_date'); // Payroll date
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
