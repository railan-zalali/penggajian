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
            $table->boolean('can_login')->default(false)->after('pekerjaan');
            $table->string('password')->nullable()->after('can_login');
            $table->string('email')->nullable()->unique()->after('password');
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->rememberToken()->after('email_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('linmas', function (Blueprint $table) {
            $table->dropColumn(['can_login', 'password', 'email', 'email_verified_at', 'remember_token']);
        });
    }
};