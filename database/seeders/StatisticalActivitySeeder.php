<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\StatisticalActivity;
use Carbon\Carbon;

class StatisticalActivitySeeder extends Seeder
{
    public function run(): void
    {
        foreach (range(1, 20) as $i) {
            StatisticalActivity::create([
                'id' => Str::uuid(),
                'name' => "Kegiatan Statistik {$i}",
                'start_date' => Carbon::now()->subDays(10),
                'end_date' => Carbon::now()->addDays(30),
                'total_target' => rand(50, 200),
                'is_done' => false,
                'is_active' => true,
            ]);
        }
    }
}
