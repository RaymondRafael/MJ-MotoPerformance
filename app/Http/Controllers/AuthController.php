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
        // 1. Validasi inputan form dengan Custom Rule Domain
        $credentials = $request->validate([
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

        // 2. Cek apakah email dan password cocok dengan database
        if (Auth::attempt($credentials)) {
            
            // Hindari serangan pencurian sesi (Session Fixation)
            $request->session()->regenerate();

            // 3. REDIRECT BERDASARKAN ROLE
            if (Auth::user()->role === 'admin') {
                // Jika yang login adalah Admin, lempar ke Dasbor Admin
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
        
        return redirect('/');
    }

    // Menampilkan halaman form registrasi pelanggan
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // 1. REGISTRASI VIA WEBSITE 
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'unique:users,email',
                function ($attribute, $value, $fail) {
                    $allowedDomains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'icloud.com'];
                    $domain = explode('@', $value)[1] ?? '';
                    if (!in_array(strtolower($domain), $allowedDomains)) {
                        $fail('Pendaftaran hanya diizinkan menggunakan email Gmail, Yahoo, Outlook, atau iCloud.');
                    }
                },
            ],
            // ATURAN NOMOR WHATSAPP (WEB)
            'phone_number' => [
                'bail',
                'required',
                'string',
                'regex:/^(08|628|\+628|8)[0-9]*$/',
                'min:10',
                'max:15',
                'unique:customers,phone_number'
            ],
            'address' => 'nullable|string', 
            'password' => 'required|min:6|confirmed', 
        ], [
            // KAMUS ERROR
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid (harus mengandung @).',
            'email.unique' => 'Email ini sudah terdaftar di sistem kami.',
            'phone_number.required' => 'Nomor WhatsApp wajib diisi.',
            'phone_number.regex' => 'Format nomor tidak valid. Gunakan HANYA ANGKA (tanpa spasi/simbol - . *) dan awali dengan 08 atau 628.',
            'phone_number.min' => 'Nomor terlalu pendek. Minimal 10 angka.',
            'phone_number.max' => 'Nomor terlalu panjang. Maksimal 15 angka.',
            'phone_number.unique' => 'Nomor WhatsApp ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.'
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

        // Sistem mengirim WA
        $this->kirimWelcomeWA($request->phone_number, $request->name, 'Website');

        Auth::login($user);

        return redirect('/tracking')->with('success', 'Registrasi berhasil! Selamat datang di MJ MotoPerformance.');
    }


    // FITUR LUPA PASSWORD & RESET PASSWORD
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

    
    // Kirim pesan selamat datang via WhatsApp
    private function kirimWelcomeWA($phone, $name, $platform)
    {   
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($cleanPhone, 0, 1) == '0') {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        } elseif (substr($cleanPhone, 0, 1) == '8') {
            $cleanPhone = '62' . $cleanPhone;
        }

        $pesan = "Halo *{$name}*, selamat datang di MJ MotoPerformance!\n\n";
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