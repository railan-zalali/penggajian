<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateMonthlySalariesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $salaries = [
            'Kades' => 3000000.00,
            'Sekdes' => 2500000.00,
            'Bendahara' => 2200000.00,
            'Perangkat Lainnya' => 2200000.00,
        ];

        foreach ($salaries as $position => $monthlyRate) {
            DB::table('position_salary_rates')
                ->where('position', $position)
                ->update([
                    'monthly_rate' => $monthlyRate,
                    'updated_at' => Carbon::now(),
                ]);
        }
    }
}
