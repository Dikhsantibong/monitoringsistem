<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use RealRashid\SweetAlert\Facades\Alert;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'unit' => 'required',
        ]);

        // Simpan unit ke sesi
        $unit = $request->input('unit');
        session(['unit' => $unit]);

        // Set koneksi database sementara untuk autentikasi
        Config::set('database.default', $unit);

        // Attempt login
        if (Auth::attempt($request->only('email', 'password'))) {
            if (Auth::user()->role == 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('user.dashboard');
            }
        }

        return back()->withErrors(['email' => 'Login failed!']);
    }

    public function showLoginForm(Request $request) 
    {
        // Ambil parameter unit dari URL jika ada
        $selectedUnit = $request->query('unit');
        
        return view('auth.login', [
            'selectedUnit' => $selectedUnit
        ]);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        Alert::error('Login Gagal', 'Email atau password salah!');
        return back()->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        Alert::success('Berhasil Logout', 'Anda telah berhasil keluar dari sistem');
        return redirect('/');
    }
} 