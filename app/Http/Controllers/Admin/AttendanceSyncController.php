<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendanceSyncController extends Controller
{
    public function sync()
    {
        $data = Http::get('https://absen-monday.online/api/attendance/all')->json();

        foreach($data as $row){
            Attendance::updateOrCreate(
              ['token'=>$row['token']],
              $row
            );
        }

        return 'Sync OK';
    }
}
