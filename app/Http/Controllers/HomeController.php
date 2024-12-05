<?php

namespace App\Http\Controllers;
use App\Models\Unit;
use App\Models\News;
use App\Models\Marker;
use App\Models\Machine;
use App\Models\PowerPlant;
use App\Models\MachineOperation;

class HomeController extends Controller
{
    public function index()
    {
        // Mengambil data unit pembangkit listrik dari database
        $units = Machine::with(['powerPlant', 'operations'])->get()->toArray(); // Mengambil semua data unit dengan relasi powerPlant dan operations

        // Mengambil data marker dari database
        $markers = Marker::all()->toArray(); // Mengambil semua data marker

        // Statistik (menggunakan data dari database)
        $total_units = count($units); // Hitung total unit
        $total_capacity = array_sum(array_map(function($unit) {
            return $unit['capacity'];
        }, $units)); // Hitung total kapasitas
        $active_units = count(array_filter($units, function($unit) {
            return $unit['status'] === 'Aktif';
        })); // Hitung unit aktif

        // Contoh data untuk grafik
        $total_capacity_data = [100, 200, 150, 300]; // Ganti dengan data yang sesuai
        $total_units_data = [10, 20, 15, 30]; // Ganti dengan data yang sesuai
        $active_units_data = [5, 10, 8, 15]; // Ganti dengan data yang sesuai
        $dates = ['Jan', 'Feb', 'Mar', 'Apr']; // Ganti dengan tanggal yang sesuai

        return view('homepage', compact('units', 'markers', 'total_units', 'total_capacity', 'active_units', 'total_capacity_data', 'total_units_data', 'active_units_data', 'dates'));
    }
} 