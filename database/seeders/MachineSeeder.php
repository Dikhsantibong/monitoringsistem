<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Machine;

class MachineSeeder extends Seeder
{
    public function run()
    {
        Machine::create([
            'name' => 'Mesin A',
            'status' => 'Aktif',
            'health_status' => 'Baik',
            'operational_duration' => 1000,
        ]);

        Machine::create([
            'name' => 'Mesin B',
            'status' => 'Tidak Aktif',
            'health_status' => 'Perlu Perbaikan',
            'operational_duration' => 500,
        ]);

        Machine::create([
            'name' => 'Mesin C',
            'status' => 'Aktif',
            'health_status' => 'Kritis',
            'operational_duration' => 200,
        ]);
    }
} 