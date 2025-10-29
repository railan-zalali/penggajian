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
            $table->boolean('payment_confirmed')->default(false)->after('payment_date');
            $table->timestamp('payment_confirmed_at')->nullable()->after('payment_confirmed');
            $table->text('payment_confirmation_note')->nullable()->after('payment_confirmed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['payment_confirmed', 'payment_confirmed_at', 'payment_confirmation_note']);
        });
    }
};
