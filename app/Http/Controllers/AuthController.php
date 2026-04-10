<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

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

    // =========================================================================
    // 1. REGISTRASI VIA WEBSITE 
    // =========================================================================
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|unique:customers,phone_number',
            'address' => 'nullable|string', 
            'password' => 'required|min:6|confirmed', 
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer', 
        ]);

        Customer::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'address' => $request->address, 
        ]);

        // PASTIKAN BARIS INI ADA! Ini yang menyuruh sistem mengirim WA
        $this->kirimWelcomeWA($request->phone_number, $request->name, 'Website');

        Auth::login($user);

        return redirect('/tracking')->with('success', 'Registrasi berhasil! Selamat datang di MJ MotoPerformance.');
    }


    // Memproses data registrasi dari React (API)
    public function apiRegister(Request $request)
    {
        // 1. Validasi Input (Tambahkan address)
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|unique:customers,phone_number',
            'address' => 'nullable|string', // <-- Tambahkan validasi alamat (nullable = boleh kosong)
            'password' => 'required|min:8|confirmed',
        ]);

        // 2. Buat Akun Login (Tabel users)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer', 
        ]);

        // 3. Buat Profil Pelanggan (Tabel customers - Tambahkan address)
        Customer::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'address' => $request->address, // <-- Simpan inputan alamat dari React ke database
        ]);

        // 4. Kembalikan respons JSON
        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil! Silakan login.'
        ], 201);
    }


    // =========================================================================
    // FITUR LUPA PASSWORD & RESET PASSWORD
    // =========================================================================
    
    // 1. Menampilkan form input email (Lupa Password)
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    // 2. Mengirimkan link reset ke email pelanggan
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email'], [
            'email.exists' => 'Kami tidak dapat menemukan akun dengan alamat email tersebut.'
        ]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => 'Link reset password telah berhasil dikirim ke email Anda!'])
                    : back()->withErrors(['email' => 'Gagal mengirim link reset. Silakan coba lagi.']);
    }

    // 3. Menampilkan form input password baru (Setelah link di email diklik)
    public function showResetPasswordForm(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    // 4. Memproses penyimpanan password baru ke database
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
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

    
    // =========================================================================
    // FUNGSI BANTUAN UNTUK MENGIRIM WA (DENGAN JEBAKAN TOTAL)
    // =========================================================================
    private function kirimWelcomeWA($phone, $name, $platform)
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($cleanPhone, 0, 1) == '0') {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        } elseif (substr($cleanPhone, 0, 1) == '8') {
            $cleanPhone = '62' . $cleanPhone;
        }

        $pesan = "Halo *{$name}*, selamat datang di MJ MotoPerformance! 🏍️💨\n\n";
        $pesan .= "Terima kasih telah bergabung. Akun Anda telah berhasil didaftarkan di sistem kami.\n\n";
        $pesan .= "Mulai sekarang, segala informasi mengenai *Update Status Pengerjaan* dan *Rincian Tagihan Servis* kendaraan Anda akan dikirimkan secara otomatis melalui nomor WhatsApp ini.\n\n";
        $pesan .= "Anda juga dapat memantau riwayat servis melalui Aplikasi Mobile kami. Jika ada pertanyaan, jangan ragu untuk membalas pesan ini!\n\n";
        $pesan .= "Salam hangat,\n*MJ MotoPerformance*";

        $response = Http::withHeaders([
            'Authorization' => env('FONNTE_TOKEN') 
        ])->post('https://api.fonnte.com/send', [
            'target' => $cleanPhone,
            'message' => $pesan,
        ]);
    }
}