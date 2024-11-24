<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Cek role user
            if (Auth::user()->role === 'admin') {
                session()->flash('success', 'Login berhasil sebagai Admin!');
                return redirect()->route('admin.dashboard');
            } else {
                session()->flash('success', 'Login berhasil!');
                return redirect()->route('user.dashboard');
            }
        }

        return $this->sendFailedLoginResponse($request);
    }

    public function showLoginForm()
    {
        return view('auth.login');
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
        return redirect('/login');
    }
} 