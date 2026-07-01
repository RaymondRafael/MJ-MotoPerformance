<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class PasswordResetController extends Controller
{
    /**
     * Mengirimkan link reset password langsung ke halaman Web Formulir Baru
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Kami tidak dapat menemukan pengguna dengan alamat email tersebut.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        // 1. Buat token aman dari Laravel Broker
        $token = Password::broker()->createToken($user);

        // 2. LANGSUNG ARAHKAN KE RUTE FORM WEB (Menghilangkan rute jembatan mobile)
        $resetUrl = url("/reset-password/{$token}?email=" . urlencode($user->email));

        // 3. Kirim email 
        try {
            Mail::send([], [], function ($message) use ($user, $resetUrl) {
                $message->to($user->email)
                    ->subject('Atur Ulang Kata Sandi - MJ MotoPerformance')
                    ->html("
                        <div style='font-family: sans-serif; padding: 30px; background-color: #f8fafc; color: #334155;'>
                            <div style='max-width: 500px; margin: 0 auto; background-color: white; padding: 24px; rounded-xl; border: 1px solid #e2e8f0; border-radius: 12px;'>
                                <h2 style='color: #1e293b; margin-bottom: 8px;'>Permintaan Atur Ulang Password</h2>
                                <p style='font-size: 14px; line-height: 1.5; color: #64748b;'>Halo, kami menerima permintaan untuk mengatur ulang kata sandi akun Anda di MJ MotoPerformance.</p>
                                <p style='font-size: 14px; line-height: 1.5; color: #64748b;'>Silakan klik tautan di bawah ini untuk membuat kata sandi baru langsung melalui web browser Anda:</p>
                                
                                <div style='text-align: center; margin: 25px 0;'>
                                    <a href='{$resetUrl}' style='display: inline-block; background-color: #dc2626; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 14px;'>Atur Ulang Kata Sandi Anda</a>
                                </div>
                                
                                <p style='font-size: 12px; color: #94a3b8; border-t: 1px solid #f1f5f9; padding-top: 15px;'>Jika Anda tidak merasa melakukan permintaan ini, silakan abaikan pesan ini secara aman.</p>
                            </div>
                        </div>
                    ");
            });

            return response()->json([
                'success' => true,
                'message' => 'Tautan pemulihan kata sandi telah berhasil dikirim ke email Anda.'
            ], 200);

        } catch (\Exception $e) {
            // Jika ada masalah koneksi mailer/SMTP
            return response()->json([
                'success' => false,
                'message' => 'Sistem gagal memicu email server. Detail internal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memproses penggantian password baru (Digunakan jika frontend mobile menembak langsung)
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ], [
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(\Illuminate\Support\Str::random(60));
                $user->save();
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Kata sandi akun Anda berhasil diperbarui.'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => __($status)
        ], 400);
    }
}