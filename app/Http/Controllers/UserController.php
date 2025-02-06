<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Machine;
use App\Models\Meeting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\MachineStatusLog;
use App\Models\PowerPlant;
use App\Models\Notification;

class UserController extends Controller
{
    public function dashboard()
    {
        // Mengambil data untuk overview cards
        $totalMachines = Machine::count();
        $operatingMachines = MachineStatusLog::where('status', 'Operasi')
            ->whereDate('created_at', today())
            ->count();
        $troubleMachines = MachineStatusLog::where('status', 'Gangguan')
            ->whereDate('created_at', today())
            ->count();
        $maintenanceMachines = MachineStatusLog::where('status', 'Pemeliharaan')
            ->whereDate('created_at', today())
            ->count();

        // Mengambil data kinerja pembangkit dengan pengecekan pembagi nol
        $powerPlantPerformance = PowerPlant::select('name')
            ->withCount(['machines as total_machines'])
            ->withCount(['machines as operating_machines' => function($query) {
                $query->whereHas('statusLogs', function($q) {
                    $q->where('status', 'Operasi')
                        ->whereDate('created_at', today());
                });
            }])
            ->get()
            ->map(function($plant) {
                // Hindari pembagian dengan nol
                $plant->efficiency = $plant->total_machines > 0 
                    ? ($plant->operating_machines / $plant->total_machines) * 100 
                    : 0;
                return $plant;
            });

        // Mengambil aktivitas pemeliharaan terbaru
        $recentMaintenances = MachineStatusLog::with('machine')
            ->where('status', 'Pemeliharaan')
            ->whereDate('created_at', today())
            ->latest()
            ->take(5)
            ->get();

        // Mengambil meeting hari ini
        $todayMeetings = Meeting::whereDate('scheduled_at', today())
            ->orderBy('scheduled_at')
            ->get();

        // Mengambil notifikasi
        $notifications = Notification::whereDate('created_at', today())
            ->latest()
            ->take(5)
            ->get();

        return view('user.dashboard', compact(
            'totalMachines',
            'operatingMachines',
            'troubleMachines',
            'maintenanceMachines',
            'powerPlantPerformance',
            'recentMaintenances',
            'todayMeetings',
            'notifications'
        ));
    }

    public function monitoring()
    {
        return view('user.monitoring'); // Pastikan file view ini ada
    }

    public function dailyMeeting()
    {
        // Ambil semua pertemuan yang dijadwalkan untuk hari ini
        $meetings = Meeting::with(['department', 'participants'])
            ->whereDate('scheduled_at', today())
            ->get(); // Ambil semua pertemuan yang dijadwalkan untuk hari ini

        return view('user.daily-meeting', compact('meetings')); // Kirim data ke tampilan
    }

    public function support()
    {
        return view('user.support'); // Pastikan file view ini ada
    }

    public function documentation()
    {
        return view('user.documentation'); // Pastikan file view ini ada
    }

    public function profile()
    {
        return view('user.profile'); // Pastikan Anda memiliki view untuk profil pengguna
    }

    public function machineMonitor(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));

        // Ambil semua power plants dengan relasi machines dan logs
        $powerPlants = \App\Models\PowerPlant::with(['machines' => function($query) {
            $query->orderBy('name');
        }])->get();

        // Ambil semua logs untuk tanggal yang dipilih
        $machineStatusLogs = MachineStatusLog::whereDate('tanggal', $date)
            ->get();

        if ($request->ajax()) {
            $view = view('user.machine-monitor._table', compact('powerPlants', 'machineStatusLogs', 'date'))->render();
            
            return response()->json([
                'success' => true,
                'html' => $view
            ]);
        }

        return view('user.machine-monitor', compact('powerPlants', 'machineStatusLogs', 'date'));
    }

    public function meetings()
    {
        $meetings = Meeting::with(['department', 'participants'])->get(); // Ambil semua jadwal meeting dengan relasi
        return view('user.meetings', compact('meetings')); // Kirim data ke tampilan
    }
    
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            $avatarName = time().'.'.$request->avatar->extension();
            $request->avatar->move(public_path('avatars'), $avatarName);
            $user->avatar = 'avatars/'.$avatarName;
        }

        $user->save();

        return redirect()->route('user.profile')->with('success', 'Profile updated successfully.');
    }

    public function index()
    {
        $users = User::all(); // Kembali ke pengambilan data sederhana
        return view('admin.users.index', compact('users'));
    }

    
}
