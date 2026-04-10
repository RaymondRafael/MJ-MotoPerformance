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

        // Mencari berdasarkan nama atau nomor HP
        $customers = Customer::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%");
        })->latest()->get();

        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:users',
            'password'     => 'required|string|min:6',
            'phone_number' => 'required|string|unique:customers',
            'address'      => 'required|string'
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
     * Fungsi Bantuan untuk mengirim WA Sambutan
     */
    private function kirimWelcomeWA($phone, $name)
    {
        try {
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
            
            $pesan = "Halo *{$name}*, selamat datang di MJ MotoPerformance! 🏍️💨\n\n";
            $pesan .= "Terima kasih telah bergabung. Akun Anda telah berhasil didaftarkan di sistem kami.\n\n";
            $pesan .= "Mulai sekarang, segala informasi mengenai *Update Status Pengerjaan* dan *Rincian Tagihan Servis* kendaraan Anda akan dikirimkan secara otomatis melalui nomor WhatsApp ini.\n\n";
            $pesan .= "Anda juga dapat memantau riwayat servis melalui Aplikasi Mobile kami. Jika ada pertanyaan, jangan ragu untuk membalas pesan ini!\n\n";
            $pesan .= "Salam hangat,\n*Tim MJ MotoPerformance*";

            // Tembak API Fonnte secara diam-diam (background)
            Http::withHeaders([
                'Authorization' => env('FONNTE_TOKEN') 
            ])->post('https://api.fonnte.com/send', [
                'target' => $cleanPhone,
                'message' => $pesan,
                'countryCode' => '62',
            ]);
            
            // Catatan: Di sini kita tidak me-return error jika WA gagal. 
            // Kenapa? Agar jika Fonnte sedang down, akun pelanggan tetap berhasil terbuat di database.

        } catch (\Exception $e) {
            // Biarkan kosong agar tidak mengganggu proses pendaftaran
        }
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    
    public function update(Request $request, Customer $customer)
    {
        // 1. Validasi input
        $request->validate([
            'name' => 'required',
            'phone_number' => 'required|unique:customers,phone_number,' . $customer->id,
            'address' => 'nullable|string' // Tambahkan ini jika di form edit admin ada isian alamat
        ]);

        // 2. Update data profil pelanggan (Tabel customers)
        $customer->update($request->all());

        // 3. SINKRONISASI PENTING: Ikut ubah nama di tabel Users agar di React juga berubah!
        if ($customer->user_id) {
            \App\Models\User::where('id', $customer->user_id)->update([
                'name' => $request->name 
            ]);
        }

        return redirect()->route('admin.customers.index')->with('success', 'Data pelanggan dan akun login berhasil diperbarui sinkron.');
    }


    // Fungsi untuk menghapus pelanggan di Laravel Web
    public function destroy($id)
    {
        try {
            // 1. Cari data profil pelanggan
            $customer = Customer::findOrFail($id);
            
            // 2. Ambil ID akun user (tabel users) miliknya
            $userId = $customer->user_id;

            // 3. Hapus profil pelanggannya (Tabel customers)
            $customer->delete();

            // 4. Hapus akun loginnya (Tabel users) agar tidak jadi "hantu"
            if ($userId) {
                \App\Models\User::where('id', $userId)->delete();
            }

            // Jika sukses, kembalikan dengan pesan hijau
            return redirect()->back()->with('success', 'Akun pelanggan dan data login berhasil dihapus total!');

        } catch (\Illuminate\Database\QueryException $e) {
            // Jika muncul error database (kode 23000), berarti pelanggan ini masih punya kendaraan
            if ($e->getCode() == 23000) {
                return redirect()->back()->with('error', 'Gagal menghapus! Pelanggan ini masih memiliki data kendaraan. Harap hapus kendaraannya terlebih dahulu.');
            }
            
            // Error lainnya
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}