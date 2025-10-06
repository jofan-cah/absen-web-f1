<?php

namespace Database\Seeders;

use App\Models\IjinType;
use Illuminate\Database\Seeder;

class IjinTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Sakit',
                'code' => 'sick',
                'description' => 'Ijin karena sakit',
            ],
            [
                'name' => 'Cuti',
                'code' => 'annual',
                'description' => 'Cuti tahunan',
            ],
            [
                'name' => 'Pribadi',
                'code' => 'personal',
                'description' => 'Ijin keperluan pribadi',
            ],
            [
                'name' => 'Tukar Shift',
                'code' => 'shift_swap',
                'description' => 'Tukar jadwal piket ke hari lain',
            ],
            [
                'name' => 'Cuti Pengganti',
                'code' => 'compensation_leave',
                'description' => 'Kompensasi libur karena piket di hari libur',
            ],
        ];

        foreach ($types as $type) {
            IjinType::create([
                'name' => $type['name'],
                'code' => $type['code'],
                'description' => $type['description'],
                'is_active' => true,
            ]);
        }
    }
}
