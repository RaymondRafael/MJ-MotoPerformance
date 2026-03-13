<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan halaman form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Memproses permintaan Login
    public function login(Request $request)
    {
        // 1. Validasi inputan form
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 2. Cek apakah email dan password cocok dengan database
        if (Auth::attempt($credentials)) {
            
            // Hindari serangan pencurian sesi (Session Fixation)
            $request->session()->regenerate();

            // 3. LOGIKA REDIRECTION BERDASARKAN ROLE
            if (Auth::user()->role === 'admin') {
                // Jika yang login adalah Admin, lempar ke Dasbor Statistik
                return redirect()->route('admin.dashboard')->with('success', 'Selamat datang kembali, Admin!');
            } else {
                // Jika yang login adalah Pelanggan, lempar ke Tracking Servis
                return redirect('/tracking')->with('success', 'Berhasil login! Membuka Dasbor Kendaraan Anda.');
            }
        }

        // Jika email/password salah, kembalikan ke halaman login dengan pesan error
        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    // Memproses logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/'); // Kembali ke halaman utama
    }

    // Menampilkan halaman form registrasi pelanggan
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Memproses data registrasi pelanggan baru
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|unique:customers,phone_number',
            'address' => 'nullable|string', // 1. Tambahkan validasi alamat (nullable berarti boleh kosong)
            'password' => 'required|min:6|confirmed', 
        ]);

        // Buat Akun Login (Tabel users)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer', 
        ]);

        // Buat Profil Pelanggan (Tabel customers)
        Customer::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'address' => $request->address, // 2. Simpan inputan alamat ke database
        ]);

        // Otomatis login setelah berhasil daftar
        Auth::login($user);

        return redirect('/tracking')->with('success', 'Registrasi berhasil! Selamat datang di MJ MotoPerformance.');
    }
}