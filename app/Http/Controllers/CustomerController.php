<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        // UBAH: Tidak perlu lagi menggunakan with('user') atau orWhereHas.
        // Karena kolom email sekarang ada DILAM tabel customers itu sendiri!
        $customers = Customer::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"); // <-- Pencarian email langsung!
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
                'unique:customers,email',
                function ($attribute, $value, $fail) {
                    $allowedDomains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'icloud.com'];
                    $domain = explode('@', $value)[1] ?? '';
                    if (!in_array(strtolower($domain), $allowedDomains)) {
                        $fail('Pendaftaran hanya diizinkan menggunakan email Gmail, Yahoo, Outlook, atau iCloud.');
                    }
                },
            ],
            'password'     => 'required|string|min:6',
            // ATURAN NOMOR WHATSAPP
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
            'phone_number.required' => 'Nomor WhatsApp wajib diisi.',
            'phone_number.regex' => 'Format nomor tidak valid. Gunakan HANYA ANGKA (tanpa spasi/simbol - . *) and awali dengan 08 atau 628 atau +62 atau 8.',
            'phone_number.min' => 'Nomor terlalu pendek. Minimal 10 angka.',
            'phone_number.max' => 'Nomor terlalu panjang. Maksimal 15 angka.',
            'phone_number.unique' => 'Nomor WhatsApp ini sudah terdaftar.',
            'address.required' => 'Alamat domisili wajib diisi.',
        ]);

        try {
            // UBAH: Cukup 1 perintah Create saja untuk membuat Pelanggan + Passwordnya
            Customer::create([
                'name'         => $request->name,
                'email'        => $request->email,
                'password'     => Hash::make($request->password), // Password langsung disimpan di tabel customer
                'phone_number' => $request->phone_number,
                'address'      => $request->address,
            ]);

            // 3. KIRIM PESAN SELAMAT DATANG VIA WHATSAPP
            $this->kirimWelcomeWA($request->phone_number, $request->name);

            return redirect()->route('admin.customers.index')
                            ->with('success', 'Akun pelanggan berhasil dibuat dan pesan sambutan WhatsApp telah dikirim!');

        } catch (\Exception $e) {
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
            $pesan .= "Terima kasih telah bergabung. Akun Anda telah berhasil didaftarkan di sistem kami oleh Admin.\n\n";
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
            // Biarkan kosong agar jika WA gagal, pendaftaran tetap berhasil
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
            // Tambahkan validasi email jika Admin ingin mengubah email pelanggan
            'email' => 'required|email|unique:customers,email,' . $customer->id,
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
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan oleh pelanggan lain.',
            'phone_number.required' => 'Nomor WhatsApp wajib diisi.',
            'phone_number.regex' => 'Format nomor tidak valid. Gunakan HANYA ANGKA (tanpa spasi/simbol - . *) and awali dengan 08 atau 628 atau +62 atau 8.',
            'phone_number.min' => 'Nomor terlalu pendek. Minimal 10 angka.',
            'phone_number.max' => 'Nomor terlalu panjang. Maksimal 15 angka.',
            'phone_number.unique' => 'Nomor WhatsApp ini sudah terdaftar.',
            'address.required' => 'Alamat lengkap wajib diisi.',
            'address.max' => 'Alamat terlalu panjang. Maksimal 255 karakter.',
        ]);

        // 2. Update data profil pelanggan (Tabel customers)
        // Cukup satu baris ini saja, tidak perlu update ke tabel users lagi!
        $customer->update($request->only(['name', 'email', 'phone_number', 'address']));

        return redirect()->route('admin.customers.index')->with('success', 'Data pelanggan dan akun login berhasil diperbarui.');
    }

    // Hapus Pelanggan
    public function destroy($id)
    {
        try {
            // 1. Cari data profil pelanggan
            $customer = Customer::findOrFail($id);
            
            // 2. Hapus profil pelanggannya (Ini otomatis menghapus hak akses loginnya juga)
            $customer->delete();
            
            // Logika penghapusan ke tabel Users DIHAPUS karena sudah pisah rumah!

            return redirect()->back()->with('success', 'Akun pelanggan dan data login berhasil dihapus total!');

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return redirect()->back()->with('error', 'Gagal menghapus! Pelanggan ini masih memiliki data kendaraan. Harap hapus kendaraannya terlebih dahulu.');
            }
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}