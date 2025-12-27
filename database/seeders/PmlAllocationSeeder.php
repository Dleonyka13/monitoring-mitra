<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\PmlAllocation;
use App\Models\User;
use App\Models\StatisticalActivity;

class PmlAllocationSeeder extends Seeder
{
    public function run(): void
    {
        $pegawai = User::where('role', 'pegawai')->get();
        $activities = StatisticalActivity::all();

        foreach ($pegawai as $user) {
            foreach ($activities->random(3) as $activity) {
                PmlAllocation::create([
                    'id' => Str::uuid(),
                    'user_id' => $user->id,
                    'statistical_activity_id' => $activity->id,
                ]);
            }
        }
    }
}
