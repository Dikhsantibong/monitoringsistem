<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MachineIssue;

class IssueSeeder extends Seeder
{
    public function run()
    {
        MachineIssue::create([
            'description' => 'Mesin A tidak berfungsi',
            'status' => 'Open',
            'machine_id' => 1, // ID mesin A
        ]);

        MachineIssue::create([
            'description' => 'Suara aneh dari Mesin B',
            'status' => 'In Progress',
            'machine_id' => 2, // ID mesin B
        ]);
    }
}
