<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class BauBauUserSeeder extends Seeder
{
    public function run()
    {
        // Set koneksi database ke u478221055_ulpltd_bau_bau
        DB::setDefaultConnection('u478221055_ulpltd_bau_bau');
        
        $csvFile = fopen(base_path("database/data/bau_bau.csv"), "r");
        
        // Skip baris header
        $firstline = true;
        
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstline) {
                User::create([
                    'name' => $data[0],
                    'email' => $data[1],
                    'password' => Hash::make($data[2]),
                    'role' => 'user',
                    // 'department_id' => null
                ]);
            }
            $firstline = false;
        }

        fclose($csvFile);
    }
}