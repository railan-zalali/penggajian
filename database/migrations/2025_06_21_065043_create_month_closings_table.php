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
        Schema::create('month_closings', function (Blueprint $table) {
            $table->id();
            $table->date('period');
            $table->integer('year');
            $table->integer('month');
            $table->date('closing_date');
            $table->integer('total_linmas');
            $table->integer('total_payrolls');
            $table->decimal('total_amount', 15, 2);
            $table->foreignId('closed_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->string('status')->default('closed'); // closed, reopened
            $table->timestamps();

            // Ensure only one closing per month/year
            $table->unique(['year', 'month']);
        });

        // Add column to payrolls table to track month closing
        Schema::table('payrolls', function (Blueprint $table) {
            $table->foreignId('month_closing_id')->nullable()->constrained('month_closings')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropForeign(['month_closing_id']);
            $table->dropColumn('month_closing_id');
        });

        Schema::dropIfExists('month_closings');
    }
};
