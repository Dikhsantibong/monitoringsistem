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

class UserController extends Controller
{
    public function dashboard()
    {
        return view('user.dashboard'); // Pastikan file view ini ada
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

        $machineStatusLogs = MachineStatusLog::with(['machine.powerPlant'])
            ->whereDate('tanggal', $date)
            ->orderBy('tanggal', 'desc')
            ->paginate(10)
            ->appends(['date' => $date]);

        if ($request->ajax()) {
            $view = view('user.machine-monitor._table', compact('machineStatusLogs'))->render();
            
            return response()->json([
                'success' => true,
                'html' => $view
            ]);
        }

        return view('user.machine-monitor', compact('machineStatusLogs'));
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
