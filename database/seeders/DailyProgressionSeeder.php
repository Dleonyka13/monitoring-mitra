<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\DailyProgression;
use App\Models\PclAllocation;

class DailyProgressionSeeder extends Seeder
{
    public function run(): void
    {
        $pcls = PclAllocation::all();

        foreach ($pcls as $pcl) {
            foreach (range(1, 5) as $i) {
                DailyProgression::create([
                    'id' => Str::uuid(),
                    'pcl_allocation_id' => $pcl->id,
                    'respondent_name' => "Responden {$i}",
                    'address' => "Alamat {$i}",
                    'long' => '106.' . rand(100000, 999999),
                    'lat' => '-6.' . rand(100000, 999999),
                    'status' => collect(['Pending', 'Diterima', 'Ditolak'])->random(),
                ]);
            }
        }
    }
}
