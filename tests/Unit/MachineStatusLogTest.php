<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\MachineStatusLog;
use App\Models\Machine;
use App\Models\PowerPlant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class MachineStatusLogTest extends TestCase
{
    use RefreshDatabase;

    protected $powerPlant;
    protected $machine;
    protected $machineStatus;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup test data
        $this->powerPlant = PowerPlant::factory()->create([
            'unit_source' => 'mysql_poasia'
        ]);

        $this->machine = Machine::factory()->create([
            'power_plant_id' => $this->powerPlant->id
        ]);

        $this->machineStatus = MachineStatusLog::factory()->create([
            'machine_id' => $this->machine->id,
            'status' => 'Operasi',
            'tanggal' => now()
        ]);
    }

    /** @test */
    public function it_syncs_from_up_kendari_to_unit_local()
    {
        // Set session sebagai UP Kendari
        Session::put('unit', 'mysql');

        // Create machine status
        $machineStatus = MachineStatusLog::create([
            'machine_id' => $this->machine->id,
            'status' => 'Gangguan',
            'tanggal' => now()
        ]);

        // Cek data di database unit lokal
        $localDB = DB::connection('mysql_poasia');
        $syncedData = $localDB->table('machine_status_logs')
                             ->where('id', $machineStatus->id)
                             ->first();

        $this->assertNotNull($syncedData);
        $this->assertEquals('Gangguan', $syncedData->status);
    }

    /** @test */
    public function it_syncs_from_unit_local_to_up_kendari()
    {
        // Set session sebagai unit lokal
        Session::put('unit', 'mysql_poasia');

        // Create machine status
        $machineStatus = MachineStatusLog::create([
            'machine_id' => $this->machine->id,
            'status' => 'Mothballed',
            'tanggal' => now()
        ]);

        // Cek data di database UP Kendari
        $upKendariDB = DB::connection('mysql');
        $syncedData = $upKendariDB->table('machine_status_logs')
                                 ->where('id', $machineStatus->id)
                                 ->first();

        $this->assertNotNull($syncedData);
        $this->assertEquals('Mothballed', $syncedData->status);
    }

    /** @test */
    public function it_handles_sync_failures_gracefully()
    {
        // Mock DB untuk memaksa error
        DB::shouldReceive('connection')
          ->andThrow(new \Exception('Connection failed'));

        // Catat log sebelum test
        Log::shouldReceive('error')
           ->once()
           ->with('Critical sync failure', \Mockery::any());

        // Create machine status (seharusnya gagal sync tapi tidak throw exception)
        $machineStatus = MachineStatusLog::create([
            'machine_id' => $this->machine->id,
            'status' => 'Gangguan',
            'tanggal' => now()
        ]);

        // Cek status sync
        $syncStats = MachineStatusLog::getSyncStats();
        $this->assertFalse($syncStats['attempts'][0]['success']);
    }

    /** @test */
    public function it_prevents_infinite_sync_loops()
    {
        // Set flag syncing
        MachineStatusLog::$isSyncing = true;

        // Create machine status
        $machineStatus = MachineStatusLog::create([
            'machine_id' => $this->machine->id,
            'status' => 'Gangguan',
            'tanggal' => now()
        ]);

        // Cek bahwa tidak ada sync yang terjadi
        $this->assertEmpty(MachineStatusLog::getSyncStats()['attempts']);
    }
} 