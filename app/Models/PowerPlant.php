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
}
