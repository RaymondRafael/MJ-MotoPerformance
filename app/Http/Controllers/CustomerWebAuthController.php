<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class CustomerWebAuthController extends Controller
{
    // BAGIAN LOGIN
    public function showLoginForm()
    {
        return view('auth.customer-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::guard('customer')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('tracking.index')->with('success', 'Berhasil masuk! Selamat datang di Dasbor Kendaraan Anda.');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    // BAGIAN REGISTER
    public function showRegisterForm()
    {
        // Ubah baris ini kembali ke register biasa
        return view('auth.register'); 
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required', 'email', 'unique:customers,email',
                function ($attribute, $value, $fail) {
                    $allowedDomains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'icloud.com'];
                    $domain = explode('@', $value)[1] ?? '';
                    if (!in_array(strtolower($domain), $allowedDomains)) {
                        $fail('Gunakan email Gmail, Yahoo, Outlook, atau iCloud.');
                    }
                },
            ],
            'phone_number' => [
                'bail', 'required', 'string', 'regex:/^(08|628|\+628|8)[0-9]*$/',
                'min:10', 'max:15', 'unique:customers,phone_number'
            ],
            'address' => 'required|string', 
            'password' => 'required|min:6|confirmed', 
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'phone_number.required' => 'Nomor WhatsApp wajib diisi.',
            'phone_number.unique' => 'Nomor WhatsApp ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.'
        ]);

        // 1. Buat data langsung ke tabel customers
        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'address' => $request->address,
        ]);

        // 2. Kirim WA
        $this->kirimWelcomeWA($request->phone_number, $request->name, 'Website');

        // 3. Langsung loginkan user tersebut
        Auth::guard('customer')->login($customer);

        // 4. Arahkan ke dashboard tracking
        return redirect()->route('tracking.index')->with('success', 'Registrasi berhasil! Selamat datang di MJ MotoPerformance.');
    }

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
        }
    }
}