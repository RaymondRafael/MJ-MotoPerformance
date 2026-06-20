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
    // Menampilkan Daftar Servis dengan Fitur Pencarian (Mendukung Snapshot Data)
    public function index(Request $request)
    {
        $search = $request->search;

        $services = Service::with(['vehicle.customer', 'mechanic'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('vehicle', function ($q) use ($search) {
                    $q->where('license_plate', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($qc) use ($search) {
                        $qc->where('name', 'like', "%{$search}%");
                    });
                })
                // PENGAMANAN & FITUR BARU: Cari juga di kolom snapshot jika master data sudah dihapus
                ->orWhere('historical_license_plate', 'like', "%{$search}%")
                ->orWhere('historical_customer_name', 'like', "%{$search}%")
                ->orWhere('complaint', 'like', "%{$search}%");
            })
            ->orderBy('id', 'asc') 
            ->paginate(10);

        
        
        return view('admin.services.index', compact('services'));
    }

    // Menampilkan Form Tambah Servis Baru
    public function create()
    {
        $vehicles = Vehicle::with('customer')->get();
        $mechanics = Mechanic::all();

        return view('admin.services.create', compact('vehicles', 'mechanics'));
    }

    // Menyimpan Data Servis Baru (Proses Perekaman Snapshot)
    public function store(Request $request)
    {
        // Kamus Error
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'mechanic_id' => 'required|exists:mechanics,id',
            'complaint' => 'required|string',
        ], [
            'vehicle_id.required' => 'Gagal: Plat kendaraan wajib dipilih terlebih dahulu.',
            'vehicle_id.exists'   => 'Gagal: Kendaraan tidak ditemukan di sistem.',
            'mechanic_id.required'=> 'Gagal: Mekanik yang bertugas wajib dipilih.',
            'mechanic_id.exists'  => 'Gagal: Mekanik tidak valid atau sudah dihapus.',
            'complaint.required'  => 'Gagal: Keluhan pelanggan tidak boleh dikosongkan.',
        ]);

        // 1. Ambil data Master Kendaraan beserta data Pelanggan saat ini
        $vehicle = Vehicle::with('customer')->findOrFail($request->vehicle_id);
        $mechanic = Mechanic::find($request->mechanic_id);

        // 2. Simpan ke database termasuk data "fotokopi" (Snapshot)
        Service::create([
            'vehicle_id' => $request->vehicle_id,
            'mechanic_id' => $request->mechanic_id,
            
            // PROSES SNAPSHOT: Mengunci riwayat data saat ini agar abadi
            'historical_customer_name'  => $vehicle->customer->name,
            'historical_customer_phone' => $vehicle->customer->phone_number,
            'historical_license_plate'  => $vehicle->license_plate,
            'historical_vehicle_motor'  => $vehicle->brand . ' ' . $vehicle->model,
            'historical_mechanic_name'  => $mechanic ? $mechanic->name : null,
            
            'complaint' => $request->complaint,
            'status' => 'pending', 
            'service_cost' => 0,   
            'total_cost' => 0,
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Antrean kendaraan berhasil ditambahkan!');
    }

    // Menampilkan Detail Servis dan Form Tambah Suku Cadang
    public function show($id)
    {
        $service = Service::with(['vehicle.customer', 'mechanic', 'details.sparepart'])->findOrFail($id);
        $spareparts = Sparepart::where('stock', '>', 0)->get();
        
        return view('admin.services.show', compact('service', 'spareparts'));
    }

    // Memperbarui Status Servis & Kirim WA menggunakan Data Pasif (Snapshot)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,finished,canceled,lunas'
        ]);

        $service = Service::findOrFail($id);
        
        // PENGAMANAN ALUR: Blokir Loncat Status
        if ($request->status === 'finished' && $service->status === 'pending') {
            return redirect()->back()->with('error', 'Aksi Ditolak: Kendaraan belum diproses. Silakan tambahkan biaya jasa atau suku cadang terlebih dahulu.');
        }

        $service->status = $request->status;
        $service->save();

        // PENGAMANAN SNAPSHOT: Ambil data dari kolom historis, jika kosong baru ambil dari relasi aktif
        $customerName  = $service->historical_customer_name ?? ($service->vehicle->customer->name ?? 'Pelanggan');
        $customerPhone = $service->historical_customer_phone ?? ($service->vehicle->customer->phone_number ?? '');
        $platNomor     = $service->historical_license_plate ?? ($service->vehicle->license_plate ?? '-');
        $motor         = $service->historical_vehicle_motor ?? ($service->vehicle ? $service->vehicle->brand . ' ' . $service->vehicle->model : 'Kendaraan');
        
        if ($request->status == 'finished') {
            $totalBiaya = 'Rp ' . number_format($service->total_cost, 0, ',', '.');

            $pesan = "Halo *{$customerName}*,\n\n";
            $pesan .= "Pengerjaan servis untuk kendaraan bermotor *{$platNomor}* ({$motor}) Anda di MJ MotoPerformance telah *SELESAI* dikerjakan.\n\n";
            $pesan .= "Total Tagihan: *{$totalBiaya}*\n\n";
            $pesan .= "Silakan datang ke bengkel kami untuk melakukan pembayaran dan pengambilan kendaraan.\n\n";
            $pesan .= "Terima kasih telah mempercayakan kendaraan Anda kepada kami! 🙏";

            return $this->kirimWhatsApp($customerPhone, $pesan, 'Status Selesai! Notifikasi WhatsApp berhasil dikirim.');
        }
        
        elseif ($request->status == 'lunas') {
            $pesan = "Halo *{$customerName}*,\n\n";
            $pesan .= "Terima kasih atas pembayaran servis kendaraan *{$platNomor}* Anda di MJ MotoPerformance. Tagihan Anda telah dinyatakan *LUNAS*.\n\n";
            $pesan .= "Semoga kendaraan Anda selalu dalam kondisi prima. Hati-hati di jalan dan sampai jumpa di servis berikutnya! 🏍️💨";

            return $this->kirimWhatsApp($customerPhone, $pesan, 'Pembayaran Lunas! Ucapan terima kasih berhasil dikirim.');
        }

        return redirect()->back()->with('success', 'Status pengerjaan kendaraan berhasil diperbarui!');
    }

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
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat mengirim WhatsApp: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // BLOK PENGAMAN EDIT TRANSAKSI
    // =========================================================================

    public function edit($id)
    {
        $service = Service::with('vehicle.customer')->findOrFail($id);

        // Tolak akses jika status sudah Selesai atau Lunas
        if (in_array($service->status, ['finished', 'lunas'])) {
            return redirect()->route('admin.services.index')->with('error', 'Akses Ditolak: Servis telah selesai. Anda tidak dapat mengubah data keluhan atau mekanik lagi.');
        }

        // PENGAMANAN: Jika data master kendaraan sudah terhapus (vehicle_id null), tolak edit agar tidak merusak relasi dropdown
        if (!$service->vehicle_id) {
            return redirect()->route('admin.services.index')->with('error', 'Akses Ditolak: Master data kendaraan untuk transaksi ini sudah dihapus.');
        }

        $vehicles = Vehicle::with('customer')->get();
        $mechanics = Mechanic::all();
        
        return view('admin.services.edit', compact('service', 'vehicles', 'mechanics'));
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        // Tolak proses simpan jika status sudah Selesai atau Lunas
        if (in_array($service->status, ['finished', 'lunas'])) {
            return redirect()->route('admin.services.index')->with('error', 'Aksi Ditolak: Transaksi servis ini telah ditutup secara permanen.');
        }

        // Pesan Error di Update
        $request->validate([
            'complaint' => 'required|string',
            'mechanic_id' => 'nullable|exists:mechanics,id',
        ], [
            'complaint.required' => 'Gagal: Keluhan pelanggan tidak boleh dikosongkan.',
            'mechanic_id.exists' => 'Gagal: Mekanik tidak valid atau sudah dihapus.',
        ]);

        // PERBARUI JEJAK NAMA MEKANIK (Jika Ada Perubahan Mekanik)
        $mechanicName = $service->historical_mechanic_name;
        if ($request->mechanic_id) {
            $mechanic = Mechanic::find($request->mechanic_id);
            if ($mechanic) {
                $mechanicName = $mechanic->name;
            }
        }

        $service->update([
            'complaint' => $request->complaint,
            'mechanic_id' => $request->mechanic_id,
            'historical_mechanic_name' => $mechanicName, 
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Informasi servis diperbarui!');
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);

        // BLOKIR PENGHAPUSAN JIKA SUDAH LUNAS ATAU SELESAI
        if (in_array($service->status, ['finished', 'lunas'])) {
            return redirect()->back()->with('error', 'Aksi Ditolak: Nota yang sudah Selesai atau Lunas telah masuk ke dalam buku besar pendapatan dan tidak boleh dihapus secara fisik demi integritas laporan keuangan.');
        }

        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Riwayat antrean servis yang belum diproses berhasil dibatalkan dan dihapus.');
    }

    // =========================================================================
    // BLOK PENGAMAN PENAMBAHAN/PENGURANGAN BIAYA DI NOTA
    // =========================================================================

    public function addSparepart(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        if (in_array($service->status, ['finished', 'lunas'])) {
            return redirect()->back()->with('error', 'Nota sudah ditutup/selesai. Anda tidak dapat menambahkan suku cadang lagi.');
        }

        $request->validate([
            'sparepart_id' => 'required|exists:spareparts,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $sparepart = Sparepart::findOrFail($request->sparepart_id);

        if ($sparepart->stock < $request->quantity) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi!');
        }

        $subtotal = $sparepart->price * $request->quantity;

        ServiceDetail::create([
            'service_id' => $service->id,
            'sparepart_id' => $sparepart->id,
            'historical_name' => $sparepart->name,
            'quantity' => $request->quantity,
            'price' => $sparepart->price,
            'subtotal' => $subtotal
        ]);

        $sparepart->decrement('stock', $request->quantity);
        $service->increment('total_cost', $subtotal);

        if ($service->status === 'pending') {
            $service->update(['status' => 'processing']);
        }

        return redirect()->back()->with('success', 'Suku cadang ditambahkan ke nota.');
    }

    public function removeSparepart($id, $detail_id)
    {
        $service = Service::findOrFail($id);

        if (in_array($service->status, ['finished', 'lunas'])) {
            return redirect()->back()->with('error', 'Aksi Ditolak: Nota sudah ditutup/selesai. Anda tidak dapat menghapus item dari nota ini.');
        }

        $detail = ServiceDetail::findOrFail($detail_id);

        if ($detail->sparepart_id) {
            $sparepart = Sparepart::find($detail->sparepart_id);
            if ($sparepart) {
                $sparepart->increment('stock', $detail->quantity);
            }
        }

        $service->decrement('total_cost', $detail->subtotal);
        $detail->delete();

        return redirect()->back()->with('success', 'Barang berhasil dihapus.');
    }

    public function updateServiceCost(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        if (in_array($service->status, ['finished', 'lunas'])) {
            return redirect()->back()->with('error', 'Aksi Ditolak: Anda tidak dapat mengubah biaya jasa karena nota servis telah ditutup.');
        }

        $request->validate([
            'service_cost' => 'required|numeric|min:0'
        ]);

        $oldCost = $service->service_cost;
        $newCost = $request->service_cost;

        $service->service_cost = $newCost;
        $service->total_cost = ($service->total_cost - $oldCost) + $newCost;

        if ($service->status === 'pending' && $newCost > 0) {
            $service->status = 'processing';
        }

        $service->save();

        return redirect()->back()->with('success', 'Biaya jasa mekanik berhasil diperbarui.');
    }
}