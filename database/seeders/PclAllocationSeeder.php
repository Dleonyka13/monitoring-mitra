<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\PclAllocation;
use App\Models\PmlAllocation;
use App\Models\User;

class PclAllocationSeeder extends Seeder
{
    public function run(): void
    {
        $mitra = User::where('role', 'mitra')->get();
        $pmls = PmlAllocation::all();

        foreach ($pmls as $pml) {
            foreach ($mitra->random(2) as $user) {
                PclAllocation::create([
                    'id' => Str::uuid(),
                    'user_id' => $user->id,
                    'pml_allocation_id' => $pml->id,
                    'statistical_activity_id' => $pml->statistical_activity_id,
                    'target' => rand(5, 20),
                ]);
            }
        }
    }
}
