<?php

use Carbon\Carbon;
use Carbon\CarbonPeriod;
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
        Schema::create('position_salary_rates', function (Blueprint $table) {
            $table->id();
            $table->string('position')->unique(); // Jabatan: Kades, Sekdes, Bendahara, dll
            $table->decimal('monthly_rate', 15, 2); // Tarif bulanan: 3jt, 2.5jt, 2.2jt, dll
            $table->decimal('daily_rate', 15, 2)->nullable(); // Tarif harian (dihitung otomatis)
            $table->integer('working_days')->default(22); // Jumlah hari kerja default
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $workingDays = CarbonPeriod::create($startOfMonth, $endOfMonth)
            ->filter(function ($date) {
                return !$date->isWeekend();
            })
            ->count();
        // Insert default values
        DB::table('position_salary_rates')->insert([
            [
                'position' => 'Kepala Desa',
                'monthly_rate' => 3000000.00,
                'daily_rate' => 136363.64, // 3jt / 22 hari
                'working_days' => $workingDays,
                'description' => 'Tarif gaji untuk Kepala Desa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'position' => 'Sekretaris Desa',
                'monthly_rate' => 2500000.00,
                'daily_rate' => 113636.36, // 2.5jt / 22 hari
                'working_days' => $workingDays,
                'description' => 'Tarif gaji untuk Sekretaris Desa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'position' => 'Kaur Keuangan',
                'monthly_rate' => 2200000.00,
                'daily_rate' => 100000.00, // 2.2jt / 22 hari
                'working_days' => $workingDays,
                'description' => 'Tarif gaji untuk Kaur Keuangan Desa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'position' => 'Kasi Pelayanan',
                'monthly_rate' => 2200000.00,
                'daily_rate' => 100000.00, // 2.2jt / 22 hari
                'working_days' => $workingDays,
                'description' => 'Tarif gaji untuk Kasi Pelayanan Desa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'position' => 'Kasi Pemerintahan',
                'monthly_rate' => 2200000.00,
                'daily_rate' => 100000.00, // 2.2jt / 22 hari
                'working_days' => $workingDays,
                'description' => 'Tarif gaji untuk Kasi Pemerintahan Desa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'position' => 'Kasi Kesejahteraan',
                'monthly_rate' => 2200000.00,
                'daily_rate' => 100000.00, // 2.2jt / 22 hari
                'working_days' => $workingDays,
                'description' => 'Tarif gaji untuk Kasi Kesejahteraan Desa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'position' => 'Kaur Perencanaan',
                'monthly_rate' => 2200000.00,
                'daily_rate' => 100000.00, // 2.2jt / 22 hari
                'working_days' => $workingDays,
                'description' => 'Tarif gaji untuk Kaur Perencanaan Desa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'position' => 'Kaur Tata Usaha Dan Umum',
                'monthly_rate' => 2200000.00,
                'daily_rate' => 100000.00, // 2.2jt / 22 hari
                'working_days' => $workingDays,
                'description' => 'Tarif gaji untuk Kaur Tata Usaha Dan Umum Desa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'position' => 'Kepala Dusun 1',
                'monthly_rate' => 2200000.00,
                'daily_rate' => 100000.00, // 2.2jt / 22 hari
                'working_days' => $workingDays,
                'description' => 'Tarif gaji untuk Kepala Dusun 1 Desa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'position' => 'Kepala Dusun 2',
                'monthly_rate' => 2200000.00,
                'daily_rate' => 100000.00, // 2.2jt / 22 hari
                'working_days' => $workingDays,
                'description' => 'Tarif gaji untuk Kepala Dusun 2 Desa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'position' => 'Kepala Dusun 3',
                'monthly_rate' => 2200000.00,
                'daily_rate' => 100000.00, // 2.2jt / 22 hari
                'working_days' => $workingDays,
                'description' => 'Tarif gaji untuk Kepala Dusun 3 Desa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('position_salary_rates');
    }
};
