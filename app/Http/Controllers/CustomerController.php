<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        // Gunakan with('user') untuk memanggil email, 
        // dan tambahkan logika orWhereHas agar admin bisa mencari berdasarkan email.
        $customers = Customer::with('user')->when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('email', 'like', "%{$search}%");
                        });
        })->latest()->paginate(10);

        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        // 1. Validasi Input Akun Baru dengan Custom Rule Domain & WhatsApp Ketat
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
                function ($attribute, $value, $fail) {
                    $allowedDomains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'icloud.com'];
                    $domain = explode('@', $value)[1] ?? '';
                    if (!in_array(strtolower($domain), $allowedDomains)) {
                        $fail('Pendaftaran hanya diizinkan menggunakan email Gmail, Yahoo, Outlook, atau iCloud.');
                    }
                },
            ],
            'password'     => 'required|string|min:6',
            // ATURAN NOMOR WHATSAPP (STORE)
            'phone_number' => [
                'bail',
                'required',
                'string',
                'regex:/^(08|628|\+628|8)[0-9]*$/',
                'min:10',
                'max:15',
                'unique:customers,phone_number'
            ],
            'address'      => 'required|string'
        ], [
            // KAMUS ERROR
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid (harus mengandung @).',
            'email.unique' => 'Email ini sudah terdaftar di sistem kami.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal harus 6 karakter.',
            
            // Kamus Error Nomor WhatsApp
            'phone_number.required' => 'Nomor WhatsApp wajib diisi.',
            'phone_number.regex' => 'Format nomor tidak valid. Gunakan HANYA ANGKA (tanpa spasi/simbol - . *) and awali dengan 08 atau 628 atau +62 atau 8.',
            'phone_number.min' => 'Nomor terlalu pendek. Minimal 10 angka.',
            'phone_number.max' => 'Nomor terlalu panjang. Maksimal 15 angka.',
            'phone_number.unique' => 'Nomor WhatsApp ini sudah terdaftar.',
            
            'address.required' => 'Alamat domisili wajib diisi.',
        ]);

        DB::beginTransaction();

        try {
            // 1. Buat Akun Login
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'customer',
            ]);

            // 2. Buat Profil Pelanggan
            Customer::create([
                'user_id'      => $user->id,
                'name'         => $request->name,
                'phone_number' => $request->phone_number,
                'address'      => $request->address,
            ]);

            DB::commit();

            // 3. KIRIM PESAN SELAMAT DATANG VIA WHATSAPP
            $this->kirimWelcomeWA($request->phone_number, $request->name);

            return redirect()->route('admin.customers.index')
                            ->with('success', 'Akun berhasil dibuat dan pesan sambutan WhatsApp telah dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Fungsi untuk mengirim welcome message via WhatsApp menggunakan API Fonnte.
     */
    private function kirimWelcomeWA($phone, $name)
    {
        try {
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
            $pesan .= "Salam hangat,\n*Tim MJ MotoPerformance*";

            // Tembak API Fonnte (background)
            Http::withHeaders([
                'Authorization' => env('FONNTE_TOKEN') 
            ])->post('https://api.fonnte.com/send', [
                'target' => $cleanPhone,
                'message' => $pesan,
                'countryCode' => '62',
            ]);

        } catch (\Exception $e) {

        }
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        // 1. Validasi Input Update dengan Aturan WhatsApp
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => [
                'bail',
                'required',
                'string',
                'regex:/^(08|628|\+628|8)[0-9]*$/',
                'min:10',
                'max:15',
                'unique:customers,phone_number,' . $customer->id
            ],
            'address' => 'required|string|max:255' 
        ], [
            // Kamus Error untuk Update
            'name.required' => 'Nama lengkap wajib diisi.',
            'phone_number.required' => 'Nomor WhatsApp wajib diisi.',
            'phone_number.regex' => 'Format nomor tidak valid. Gunakan HANYA ANGKA (tanpa spasi/simbol - . *) and awali dengan 08 atau 628 atau +62 atau 8.',
            'phone_number.min' => 'Nomor terlalu pendek. Minimal 10 angka.',
            'phone_number.max' => 'Nomor terlalu panjang. Maksimal 15 angka.',
            'phone_number.unique' => 'Nomor WhatsApp ini sudah terdaftar.',
            'address.required' => 'Alamat lengkap wajib diisi.',
            'address.max' => 'Alamat terlalu panjang. Maksimal 255 karakter.',
        ]);

        // 2. Update data profil pelanggan (Tabel customers)
        $customer->update($request->all());

        // 3. Ikut ubah nama di tabel Users agar di React juga berubah
        if ($customer->user_id) {
            \App\Models\User::where('id', $customer->user_id)->update([
                'name' => $request->name 
            ]);
        }

        return redirect()->route('admin.customers.index')->with('success', 'Data pelanggan dan akun login berhasil diperbarui sinkron.');
    }

    // Hapus Pelanggan beserta Akun Login-nya, dengan Penanganan Error jika Masih Ada Kendaraan Terkait
    public function destroy($id)
    {
        try {
            // 1. Cari data profil pelanggan
            $customer = Customer::findOrFail($id);
            
            // 2. Ambil ID akun user (tabel users) miliknya
            $userId = $customer->user_id;

            // 3. Hapus profil pelanggannya (Tabel customers)
            $customer->delete();

            // 4. Hapus akun loginnya (Tabel users)
            if ($userId) {
                \App\Models\User::where('id', $userId)->delete();
            }

            return redirect()->back()->with('success', 'Akun pelanggan dan data login berhasil dihapus total!');

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return redirect()->back()->with('error', 'Gagal menghapus! Pelanggan ini masih memiliki data kendaraan. Harap hapus kendaraannya terlebih dahulu.');
            }
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}