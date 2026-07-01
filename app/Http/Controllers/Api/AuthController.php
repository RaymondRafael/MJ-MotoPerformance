<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer; // HANYA PANGGIL CUSTOMER
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    // Fungsi Login (HANYA UNTUK PELANGGAN MOBILE)
    public function login(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) {
                    $allowedDomains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'icloud.com'];
                    $domain = explode('@', $value)[1] ?? '';
                    if (!in_array(strtolower($domain), $allowedDomains)) {
                        $fail('Domain email tidak didukung. Gunakan Gmail, Yahoo, atau Outlook.');
                    }
                },
            ],
            'password' => 'required'
        ]);

        // 1. CARI EMAIL LANGSUNG DI TABEL CUSTOMERS
        $customer = Customer::where('email', $request->email)->first();

        // 2. JIKA EMAIL TIDAK ADA ATAU PASSWORD SALAH
        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau kata sandi yang Anda masukkan salah.'
            ], 401);
        }
            
        // 3. JIKA SUKSES, BUAT TOKEN UNTUK CUSTOMER
        $token = $customer->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'role' => 'customer' // Tetap kirim role agar React Native Anda tidak bingung
                ]
            ]
        ], 200);
    }

    // Fungsi Registrasi Pelanggan Baru (Aplikasi Mobile)
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'unique:customers,email', // UBAH: PASTIKAN EMAIL UNIK DI TABEL CUSTOMERS
                function ($attribute, $value, $fail) {
                    $allowedDomains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'icloud.com'];
                    $domain = explode('@', $value)[1] ?? '';
                    if (!in_array(strtolower($domain), $allowedDomains)) {
                        $fail('Domain email tidak didukung. Gunakan Gmail, Yahoo, atau Outlook.');
                    }
                },
            ],
            'phone_number' => [
                'bail',
                'required',
                'string',
                'regex:/^(08|628|\+628|8)[0-9]*$/',
                'min:10',                        
                'max:15',                        
                'unique:customers,phone_number'   
            ],
            'address' => 'required|string', 
            'password' => 'required|min:6|confirmed', 
        ], [
            // --- KAMUS ERROR SAMA SEPERTI SEBELUMNYA ---
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid (harus mengandung @).',
            'email.unique' => 'Email ini sudah terdaftar di sistem kami.',
            'phone_number.required' => 'Nomor WhatsApp wajib diisi.',
            'phone_number.regex' => 'Format nomor tidak valid. Gunakan HANYA ANGKA (tanpa spasi/simbol - . *) dan awali dengan 08 atau 628.',
            'phone_number.min' => 'Nomor terlalu pendek. Minimal 10 angka.',
            'phone_number.max' => 'Nomor terlalu panjang. Maksimal 15 angka.',
            'phone_number.unique' => 'Nomor WhatsApp ini sudah terdaftar.',
            'address.required' => 'Alamat domisili wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.'
        ]);

        // UBAH: KITA HANYA MEMBUAT DATA DI TABEL CUSTOMERS!
        // Tidak perlu lagi membuat data di tabel users
        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Password simpan di sini
            'phone_number' => $request->phone_number,
            'address' => $request->address,
        ]);

        // PANGGIL WA DI SINI
        $this->kirimWelcomeWA($request->phone_number, $request->name, 'Aplikasi Mobile');

        // Buatkan Token dari data customer agar langsung masuk
        $token = $customer->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'role' => 'customer'
                ]
            ]
        ], 201);
    }

    // Fungsi Logout (Tetap Sama)
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ], 200);
    }

    // FUNGSI UNTUK MENGIRIM WA (Tetap Sama)
    private function kirimWelcomeWA($phone, $name, $platform)
    {
        try {
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
            if (substr($cleanPhone, 0, 1) == '0') {
                $cleanPhone = '62' . substr($cleanPhone, 1);
            } elseif (substr($cleanPhone, 0, 1) == '8') {
                $cleanPhone = '62' . $cleanPhone;
            }

            $pesan = "Halo *{$name}*, selamat datang di MJ MotoPerformance!\n\n";
            $pesan .= "Pendaftaran akun Anda melalui *{$platform}* kami telah berhasil.\n\n";
            $pesan .= "Mulai sekarang, segala informasi mengenai *Rincian Tagihan* dan *Status Pengerjaan* kendaraan Anda akan diinformasikan ke nomor ini secara otomatis.\n\n";
            $pesan .= "Salam hangat,\n*MJ MotoPerformance*";

            Http::withHeaders([
                'Authorization' => env('FONNTE_TOKEN') 
            ])->post('https://api.fonnte.com/send', [
                'target' => $cleanPhone,
                'message' => $pesan,
            ]);

        } catch (\Exception $e) {
            // Abaikan jika error agar registrasi tidak gagal
        }
    }
}