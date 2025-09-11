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
        Schema::table('month_closings', function (Blueprint $table) {
            $table->timestamp('reopened_at')->nullable()->after('closed_by');
            $table->unsignedBigInteger('reopened_by')->nullable()->after('reopened_at');
            
            $table->foreign('reopened_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('month_closings', function (Blueprint $table) {
            $table->dropForeign(['reopened_by']);
            $table->dropColumn(['reopened_at', 'reopened_by']);
        });
    }
};