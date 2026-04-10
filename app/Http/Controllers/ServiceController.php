<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Vehicle;
use App\Models\Mechanic;
use App\Models\Sparepart;
use App\Models\ServiceDetail;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Menampilkan halaman utama Dasbor Servis (Antrean)
     */
    public function index(Request $request)
    {
        $search = $request->search;

        // Mencari berdasarkan plat nomor, nama pelanggan, atau keluhan
        $services = Service::with(['vehicle.customer', 'mechanic'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('vehicle', function ($q) use ($search) {
                    $q->where('license_plate', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($qc) use ($search) {
                        $qc->where('name', 'like', "%{$search}%");
                    });
                })->orWhere('complaint', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        return view('admin.services.index', compact('services'));
    }

    /**
     * Membuka Halaman Form "Servis Baru"
     */
    public function create()
    {
        // Mengambil semua data kendaraan (lengkap dengan nama pemiliknya) dan data mekanik
        // Data ini dikirim ke form untuk dijadikan pilihan (Dropdown / Select)
        $vehicles = Vehicle::with('customer')->get();
        $mechanics = Mechanic::all();

        return view('admin.services.create', compact('vehicles', 'mechanics'));
    }

    /**
     * Menyimpan data antrean servis baru ke Database
     */
    public function store(Request $request)
    {
        // 1. Validasi inputan form
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'mechanic_id' => 'required|exists:mechanics,id',
            'complaint' => 'required|string',
        ]);

        // 2. Simpan ke tabel services
        Service::create([
            'vehicle_id' => $request->vehicle_id,
            'mechanic_id' => $request->mechanic_id,
            'complaint' => $request->complaint,
            'status' => 'pending', // Otomatis masuk antrean (Menunggu)
            'service_cost' => 0,   // Biaya awal 0, nanti diisi saat pengerjaan
            'total_cost' => 0,
        ]);

        // 3. Kembalikan ke halaman utama dengan pesan sukses
        return redirect()->route('admin.services.index')->with('success', 'Antrean kendaraan berhasil ditambahkan!');
    }

    /**
     * Menampilkan Detail Nota Servis (Untuk melihat Rincian Suku Cadang)
     */
    public function show($id)
    {
        $service = Service::with(['vehicle.customer', 'mechanic', 'details.sparepart'])->findOrFail($id);
        
        // Mengambil suku cadang yang stoknya masih ada (lebih dari 0)
        $spareparts = Sparepart::where('stock', '>', 0)->get();
        
        return view('admin.services.show', compact('service', 'spareparts'));
    }

    /**
     * Mengubah Status Pengerjaan & Memicu Notifikasi WhatsApp Gateway
     */
    public function updateStatus(Request $request, $id)
    {
        // 1. Tambahkan 'lunas' ke dalam aturan validasi
        $request->validate([
            'status' => 'required|in:pending,processing,finished,canceled,lunas'
        ]);

        $service = Service::with('vehicle.customer')->findOrFail($id);
        $service->status = $request->status;
        $service->save();

        $customerName = $service->vehicle->customer->name;
        $customerPhone = $service->vehicle->customer->phone_number;
        $platNomor = $service->vehicle->license_plate;
        
        // LOGIKA WA 1: Jika status diubah menjadi "SELESAI" (Minta Ambil Motor)
        if ($request->status == 'finished') {
            
            $motor = $service->vehicle->brand . ' ' . $service->vehicle->model;
            $totalBiaya = 'Rp ' . number_format($service->total_cost, 0, ',', '.');

            $pesan = "Halo *{$customerName}*,\n\n";
            $pesan .= "Pengerjaan servis untuk kendaraan bermotor *{$platNomor}* ({$motor}) Anda di MJ MotoPerformance telah *SELESAI* dikerjakan.\n\n";
            $pesan .= "Total Tagihan: *{$totalBiaya}*\n\n";
            $pesan .= "Silakan datang ke bengkel kami untuk melakukan pembayaran dan pengambilan kendaraan.\n\n";
            $pesan .= "Terima kasih telah mempercayakan kendaraan Anda kepada kami! 🙏";

            return $this->kirimWhatsApp($customerPhone, $pesan, 'Status selesai! Notifikasi WhatsApp untuk pengambilan kendaraan berhasil dikirim.');
        }
        
        // LOGIKA WA 2: Jika status diubah menjadi "LUNAS" (Ucapan Terima Kasih)
        elseif ($request->status == 'lunas') {
            
            $pesan = "Halo *{$customerName}*,\n\n";
            $pesan .= "Terima kasih atas pembayaran servis kendaraan *{$platNomor}* Anda di MJ MotoPerformance. Tagihan Anda telah dinyatakan *LUNAS*.\n\n";
            $pesan .= "Semoga kendaraan Anda selalu dalam kondisi prima. Hati-hati di jalan dan sampai jumpa di servis berikutnya! 🏍️💨";

            return $this->kirimWhatsApp($customerPhone, $pesan, 'Pembayaran Lunas! Ucapan terima kasih berhasil dikirim ke WhatsApp pelanggan.');
        }

        // Jika status yang dipilih 'pending' atau 'processing', cukup tampilkan pesan biasa
        return redirect()->back()->with('success', 'Status pengerjaan kendaraan berhasil diperbarui!');
    }

    /**
     * FUNGSI BANTUAN (Helper) UNTUK MENGIRIM WA AGAR KODE LEBIH RAPI
     */
    private function kirimWhatsApp($phone, $pesan, $successMessage)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => env('FONNTE_TOKEN') 
            ])->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $pesan,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                return redirect()->back()->with('success', $successMessage);
            } else {
                return redirect()->back()->with('error', 'Status tersimpan, namun gagal mengirim WhatsApp. Pastikan Device Fonnte terkoneksi.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat mencoba mengirim WhatsApp: ' . $e->getMessage());
        }
    }

    /**
     * Form Edit Servis (Disesuaikan dengan tampilan baru)
     */
    public function edit($id)
    {
        // Load relasi vehicle.customer agar nama dan plat bisa ditampilkan di form edit
        $service = Service::with('vehicle.customer')->findOrFail($id);
        
        $vehicles = Vehicle::with('customer')->get();
        $mechanics = Mechanic::all();
        
        return view('admin.services.edit', compact('service', 'vehicles', 'mechanics'));
    }

    /**
     * Menyimpan perubahan Edit Servis (Tanpa Catatan Mekanik)
     */
    public function update(Request $request, $id)
    {
        // 1. Validasi input (Hanya Keluhan dan Mekanik)
        $request->validate([
            'complaint' => 'required|string',
            'mechanic_id' => 'nullable|exists:mechanics,id',
        ]);

        $service = Service::findOrFail($id);

        // 2. Simpan Perubahan
        $service->update([
            'complaint' => $request->complaint,
            'mechanic_id' => $request->mechanic_id,
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Informasi servis berhasil diperbarui!');
    }

    /**
     * Menghapus Riwayat Servis
     */
    public function destroy($id)
    {
        Service::findOrFail($id)->delete();
        return redirect()->route('admin.services.index')->with('success', 'Riwayat servis dihapus!');
    }

    /**
     * Menambahkan Suku Cadang ke Nota dan Memotong Stok
     */
    public function addSparepart(Request $request, $id)
    {
        $request->validate([
            'sparepart_id' => 'required|exists:spareparts,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $service = Service::findOrFail($id);
        $sparepart = Sparepart::findOrFail($request->sparepart_id);

        // Validasi: Apakah stoknya cukup?
        if ($sparepart->stock < $request->quantity) {
            return redirect()->back()->with('error', 'Stok ' . $sparepart->name . ' tidak mencukupi! Sisa stok: ' . $sparepart->stock);
        }

        // Kalkulasi Subtotal
        $subtotal = $sparepart->price * $request->quantity;

        // 1. Masukkan ke keranjang (tabel service_details)
        ServiceDetail::create([
            'service_id' => $service->id,
            'sparepart_id' => $sparepart->id,
            'quantity' => $request->quantity,
            'price' => $sparepart->price,
            'subtotal' => $subtotal
        ]);

        // 2. Potong stok suku cadang di gudang
        $sparepart->decrement('stock', $request->quantity);

        // 3. Tambahkan ke Total Tagihan Servis
        $service->increment('total_cost', $subtotal);

        return redirect()->back()->with('success', 'Suku cadang ditambahkan ke nota.');
    }

    /**
     * Menghapus Suku Cadang dari Nota dan Mengembalikan Stok
     */
    public function removeSparepart($id, $detail_id)
    {
        $service = Service::findOrFail($id);
        $detail = ServiceDetail::findOrFail($detail_id);
        $sparepart = Sparepart::findOrFail($detail->sparepart_id);

        // 1. Kembalikan stok ke gudang
        $sparepart->increment('stock', $detail->quantity);

        // 2. Kurangi Total Tagihan Servis
        $service->decrement('total_cost', $detail->subtotal);

        // 3. Hapus data dari nota
        $detail->delete();

        return redirect()->back()->with('success', 'Suku cadang dihapus dan stok dikembalikan.');
    }

    /**
     * Memperbarui Biaya Jasa Mekanik
     */
    public function updateServiceCost(Request $request, $id)
    {
        $request->validate([
            'service_cost' => 'required|numeric|min:0'
        ]);

        $service = Service::findOrFail($id);
        
        // Simpan biaya jasa yang lama untuk selisih kalkulasi
        $oldCost = $service->service_cost;
        $newCost = $request->service_cost;

        // Update biaya jasa dan sesuaikan total tagihan akhirnya
        $service->service_cost = $newCost;
        $service->total_cost = ($service->total_cost - $oldCost) + $newCost;
        $service->save();

        return redirect()->back()->with('success', 'Biaya jasa mekanik berhasil diperbarui.');
    }
}