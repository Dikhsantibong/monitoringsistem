<?php

namespace App\Http\Controllers;
use App\Models\Unit;
use App\Models\News;
use App\Models\Marker;

class HomeController extends Controller
{
    public function index()
    {
        // Data unit pembangkit listrik (contoh data statis)
        $units = [
            (object)[
                'name' => 'Unit Pembangkit 1',
                'latitude' => -4.0096,
                'longitude' => 122.512,
                'status' => 'Aktif',
                'capacity' => 100
            ],
            (object)[
                'name' => 'Unit Pembangkit 2',
                'latitude' => -4.0196,
                'longitude' => 122.522,
                'status' => 'Tidak Aktif',
                'capacity' => 150
            ],
            (object)[
                'name' => 'Unit Pembangkit 3',
                'latitude' => -4.0296,
                'longitude' => 122.532,
                'status' => 'Aktif',
                'capacity' => 200
            ],
        ];

        // Ambil data marker dari database
        $markers = Marker::all()->toArray(); // Mengambil semua data marker

        // Statistik (contoh data statis)
        $total_units = count($units); // Hitung total unit
        $total_capacity = array_sum(array_map(function($unit) {
            return $unit->capacity;
        }, $units)); // Hitung total kapasitas
        $active_units = count(array_filter($units, function($unit) {
            return $unit->status === 'Aktif';
        })); // Hitung unit aktif

        // Kontribusi Energi Hijau (contoh data statis)
        $green_energy_contribution = 0.5; // Nilai kontribusi energi hijau

        // Berita (contoh data statis)
        $news = [
            (object)[
                'id' => 1,
                'title' => 'Berita Terbaru 1',
                'summary' => 'Ini adalah cuplikan berita terbaru 1.',
                'thumbnail' => 'https://via.placeholder.com/150',
                'created_at' => now()
            ],
            (object)[
                'id' => 2,
                'title' => 'Berita Terbaru 2',
                'summary' => 'Ini adalah cuplikan berita terbaru 2.',
                'thumbnail' => 'https://via.placeholder.com/150',
                'created_at' => now()
            ],
            (object)[
                'id' => 3,
                'title' => 'Berita Terbaru 3',
                'summary' => 'Ini adalah cuplikan berita terbaru 3.',
                'thumbnail' => 'https://via.placeholder.com/150',
                'created_at' => now()
            ],
        ];

        // Testimoni (contoh data statis)
        $testimonials = [
            (object)[
                'name' => 'John Doe',
                'message' => 'Pembangkit listrik ini sangat membantu masyarakat.'
            ],
            (object)[
                'name' => 'Jane Smith',
                'message' => 'Saya bangga menjadi bagian dari proyek ini.'
            ],
        ];

        // Program CSR (contoh data statis)
        $csrPrograms = [
            (object)[
                'title' => 'Program Pendidikan',
                'description' => 'Memberikan beasiswa kepada siswa berprestasi.'
            ],
            (object)[
                'title' => 'Program Lingkungan',
                'description' => 'Kegiatan penanaman pohon di daerah sekitar.'
            ],
        ];

        // Foto (contoh data statis)
        $photos = [
            (object)['url' => 'https://via.placeholder.com/150'],
            (object)['url' => 'https://via.placeholder.com/150'],
            (object)['url' => 'https://via.placeholder.com/150'],
        ];

        // Partner (contoh data statis)
        $partners = [
            (object)['logo' => 'https://via.placeholder.com/100'],
            (object)['logo' => 'https://via.placeholder.com/100'],
            (object)['logo' => 'https://via.placeholder.com/100'],
        ];

        // Blog Edukasi (contoh data statis)
        $educationalBlogs = [
            (object)[
                'id' => 1,
                'title' => 'Cara Kerja Pembangkit Listrik',
                'summary' => 'Artikel ini menjelaskan cara kerja pembangkit listrik.'
            ],
            (object)[
                'id' => 2,
                'title' => 'Tips Hemat Listrik',
                'summary' => 'Beberapa tips untuk menghemat penggunaan listrik.'
            ],

            (object)[
                'id' => 3,
                'title' => 'Manfaat Penggunaan Energi Terbarukan',
                'summary' => 'Penggunaan energi terbarukan dapat mengurangi ketergantungan pada sumber daya alam yang tidak terbarukan.'
            ],
        ];

        return view('homepage', compact('units', 'markers', 'total_units', 'total_capacity', 'active_units', 'green_energy_contribution', 'news', 'testimonials', 'csrPrograms', 'photos', 'partners', 'educationalBlogs'));
    }
} 