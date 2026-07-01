<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Menampilkan halaman form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Memproses permintaan Login (Satu Pintu untuk Semua)
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 1. Coba login sebagai ADMIN (Cek tabel users)
        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard')->with('success', 'Selamat datang kembali, Admin!');
        }

        // 2. Coba login sebagai PELANGGAN (Cek tabel customers)
        if (Auth::guard('customer')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('tracking.index')->with('success', 'Berhasil masuk! Selamat datang di Dasbor Kendaraan Anda.');
        }

        // 3. Jika email tidak ditemukan di kedua tabel
        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    // Memproses logout untuk siapa saja yang sedang login
    public function logout(Request $request)
    {
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        } elseif (Auth::guard('customer')->check()) {
            Auth::guard('customer')->logout();
        }
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }

    // FITUR LUPA PASSWORD 
    // 1. Menampilkan form input email (Lupa Password)
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    // 2. Mengirimkan link reset secara dinamis (Admin atau Pelanggan)
    public function sendResetLinkEmail(Request $request)
    {
        // Validasi format email dasar (tidak mengunci ke salah satu tabel dulu)
        $request->validate(['email' => 'required|email']);

        // Cek lokasi keberadaan email di kedua tabel
        $isAdmin = \App\Models\User::where('email', $request->email)->exists();
        $isCustomer = \App\Models\Customer::where('email', $request->email)->exists();

        // Jika email tidak terdaftar di sistem sama sekali
        if (!$isAdmin && !$isCustomer) {
            return back()->withErrors(['email' => 'Kami tidak dapat menemukan akun dengan alamat email tersebut.']);
        }

        // Tentukan password broker secara otomatis
        // Pastikan nama broker ini sesuai dengan konfigurasi config/auth.php
        $broker = $isAdmin ? 'users' : 'customers';

        // Kirim link reset menggunakan broker yang tepat
        $status = Password::broker($broker)->sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => 'Link reset password telah berhasil dikirim ke email Anda!'])
                    : back()->withErrors(['email' => 'Gagal mengirim link reset. Silakan coba lagi.']);
    }

    // 3. Menampilkan form input password baru saat link di email diklik
    public function showResetPasswordForm(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    // 4. Memproses penyimpanan password baru ke database secara dinamis
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        // Identifikasi kembali entitas pemilik email sebelum mereset
        $isAdmin = \App\Models\User::where('email', $request->email)->exists();
        $broker = $isAdmin ? 'users' : 'customers';

        // Proses reset menggunakan konfigurasi broker yang sesuai
        $status = Password::broker($broker)->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();
                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('success', 'Password Anda berhasil diperbarui! Silakan masuk.')
                    : back()->withErrors(['email' => 'Token reset tidak valid atau kedaluwarsa.']);
    }
}