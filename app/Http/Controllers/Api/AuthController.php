<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\Http; // <-- WAJIB TAMBAHKAN INI UNTUK FONNTE

class AuthController extends Controller
{
    // Fungsi Login (Menangani Admin & Pelanggan)
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Buat Token API menggunakan Sanctum
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role // Sangat penting untuk membedakan jalur di React
                    ]
                ]
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email atau kata sandi yang Anda masukkan salah.'
        ], 401);
    }

    // Fungsi Registrasi Pelanggan Baru (Aplikasi Mobile)
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|unique:customers,phone_number',
            'address' => 'required|string', 
            'password' => 'required|min:6|confirmed', 
        ]);

        // 1. Buat Akun Login (Tabel users)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer', 
        ]);

        // 2. Buat Profil Pelanggan & SIMPAN ALAMAT (Tabel customers)
        Customer::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
        ]);

        // 3. PANGGIL ROBOT WA DI SINI (Sebelum membalas ke React Native)
        $this->kirimWelcomeWA($request->phone_number, $request->name, 'Aplikasi Mobile');

        // 4. Buatkan Token agar langsung masuk tanpa perlu login ulang
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ]
            ]
        ], 201);
    }

    // Fungsi Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ], 200);
    }

    // =========================================================================
    // FUNGSI BANTUAN UNTUK MENGIRIM WA (MODE SILENT)
    // =========================================================================
    private function kirimWelcomeWA($phone, $name, $platform)
    {
        try {
            // 1. Bersihkan dan format nomor
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
            if (substr($cleanPhone, 0, 1) == '0') {
                $cleanPhone = '62' . substr($cleanPhone, 1);
            } elseif (substr($cleanPhone, 0, 1) == '8') {
                $cleanPhone = '62' . $cleanPhone;
            }

            // 2. Siapkan Pesan
            $pesan = "Halo *{$name}*, selamat datang di MJ MotoPerformance! 🏍️💨\n\n";
            $pesan .= "Pendaftaran akun Anda melalui *{$platform}* kami telah berhasil.\n\n";
            $pesan .= "Mulai sekarang, segala informasi mengenai *Rincian Tagihan* dan *Status Pengerjaan* kendaraan Anda akan diinformasikan ke nomor ini secara otomatis.\n\n";
            $pesan .= "Salam hangat,\n*MJ MotoPerformance*";

            // 3. Tembak Fonnte secara diam-diam
            Http::withHeaders([
                'Authorization' => env('FONNTE_TOKEN') 
            ])->post('https://api.fonnte.com/send', [
                'target' => $cleanPhone,
                'message' => $pesan,
            ]);

        } catch (\Exception $e) {
            // Biarkan kosong. Jika Fonnte sedang mati, HP tidak akan nyangkut loading.
        }
    }
}