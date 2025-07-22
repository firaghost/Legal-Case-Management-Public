<?php

namespace Database\Seeders;

use App\Models\WorkUnit;
use Illuminate\Database\Seeder;

class WorkUnitSeeder extends Seeder
{
    public function run(): void
    {
        $workUnits = [
            [
                'name' => 'Legal Department',
                'code' => 'LD',
                'is_active' => true,
            ],
            [
                'name' => 'Compliance',
                'code' => 'CPL',
                'is_active' => true,
            ],
            [
                'name' => 'Risk Management',
                'code' => 'RM',
                'is_active' => true,
            ],
        ];

        foreach ($workUnits as $workUnit) {
            WorkUnit::updateOrCreate(
                ['code' => $workUnit['code']],
                $workUnit
            );
        }
    }
}






