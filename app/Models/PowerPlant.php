<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PowerPlant extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika tidak sesuai dengan konvensi
    protected $table = 'power_plants';

    // Tentukan kolom yang dapat diisi
    protected $fillable = [
        'name',
        'latitude',
        'longitude',
    ];

    public function machines()
    {
        return $this->hasMany(Machine::class, 'power_plant_id');
    }

    // Tambahkan metode untuk mendapatkan unit berdasarkan nama
    public function getMachinesByName($name)
    {
        return $this->machines()->where('name', $name)->get();
    }
    public function getConnectionName()
    {
        // Mengambil unit yang dipilih dari session dan mengatur koneksi sesuai unit
        return session('unit', 'u478221055_up_kendari'); // default ke 'up_kendari' jika tidak ada
    }
}

