<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Data yang akan dimasukkan
        $users = [
            ['name' => 'EKO YULI WIDYATMOKO', 'email' => 'eko.widyatmoko@plnnusantarapower.co.id', 'password' => '123456'],
            ['name' => 'SABAN LABABU', 'email' => 'saban.lababu@plnnusantarapower.co.id', 'password' => '123456'],
            ['name' => 'MARIA TAKKE', 'email' => 'maria.takke@plnnusantarapower.co.id', 'password' => '123456'],
            ['name' => 'AHMAD', 'email' => 'ahmad056@plnnusantarapower.co.id', 'password' => '123456'],
            ['name' => 'WAHID MIFTAKHUL KHOIR', 'email' => 'wahid.khoir@plnnusantarapower.co.id', 'password' => '123456'],
            ['name' => 'BAKRI WAHID', 'email' => 'bakri.wahid@plnnusantarapower.co.id', 'password' => '123456'],
            ['name' => 'LA ODE SYAIFUL', 'email' => 'la.syaiful@plnnusantarapower.co.id', 'password' => '123456'],
            ['name' => 'ZULFAN ANDRIYANTO', 'email' => 'zulfan.andriyanto@plnnusantarapower.co.id', 'password' => '123456'],
            ['name' => 'ALI RAHMAD', 'email' => 'ali.rahmad@plnnusantarapower.co.id', 'password' => '123456'],
            ['name' => 'MADE PRAMANA ILIANA', 'email' => 'made.pramana@plnnusantarapower.co.id', 'password' => '123456'],
            ['name' => 'RAMLIN JAMALUDIN', 'email' => 'ramlin.jamaludin@plnnusantarapower.co.id', 'password' => '123456'],
            ['name' => 'CHOIRI ASTA GANDHI', 'email' => 'choiri.asta@plnnusantarapower.co.id', 'password' => '123456'],
            ['name' => 'RIAN FENDRA LUBAYA TOMO', 'email' => 'rian.fendra@plnnusantarapower.co.id', 'password' => '123456'],
            ['name' => 'ZUL FIKAR', 'email' => 'zul.fikar@plnnusantarapower.co.id', 'password' => '123456'],
            ['name' => 'ANVIKTELJI HARUN', 'email' => 'anviktelji@plnnusantarapower.co.id', 'password' => '123456'],
            ['name' => 'ATA TAYEB', 'email' => 'ata.tayeb@plnnusantarapower.co.id', 'password' => '123456'],
            ['name' => 'WAHYU MOHAMMAD FADILLAH', 'email' => 'wahyu.fadillah@plnnusantarapower.co.id', 'password' => '123456'],
            ['name' => 'ZUL SHAFAR SARIAH', 'email' => 'zul.shafar@plnnusantarapower.co.id', 'password' => '123456'],
            ['name' => 'DANDI PRADANA DAING', 'email' => 'dandi.pradana@plnnusantarapower.co.id', 'password' => '123456'],
            ['name' => 'INDRA MULYADI', 'email' => 'indra.mulyadi@plnnusantarapower.co.id', 'password' => '123456'],
            ['name' => 'AZMAN HAMDAN', 'email' => 'azman.hamdan@plnnusantarapower.co.id', 'password' => '123456'],
            ['name' => 'DEWA TRIARYA GANESHA', 'email' => 'dewa.triarya@plnnusantarapower.co.id', 'password' => '123456'],
            ['name' => 'ASFAR ADRIN ASLI', 'email' => 'asfar.adrin@plnnusantarapower.co.id', 'password' => '123456'],
            ['name' => 'LA ONAI', 'email' => 'la.onai@plnnusantarapower.co.id', 'password' => '123456'],
            ['name' => 'PUTU WISNA ADNYANA', 'email' => 'putu.wissa@plnnusantarapower.co.id', 'password' => '123456'],
            ['name' => 'ARIF RAHMAD SIDIQ DUGRO', 'email' => 'arif.dugro@plnnusantarapower.co.id', 'password' => '123456'],
        ];

        // Menyimpan data ke tabel users
        foreach ($users as $user) {
            DB::table('users')->insert([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => bcrypt($user['password']), // Menggunakan bcrypt untuk password
                'role' => 'user', // Menetapkan role default
                'email_verified_at' => null, // Mengatur email_verified_at ke null
                'remember_token' => null, // Mengatur remember_token ke null
                'created_at' => now(), // Mengatur created_at ke waktu sekarang
                'updated_at' => now(), // Mengatur updated_at ke waktu sekarang
                'unit' => 'PLTD_KOLAKA', // Menetapkan unit
            ]);
        }
    }
}
