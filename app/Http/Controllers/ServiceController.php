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
    // Menampilkan Daftar Servis dengan Fitur Pencarian 
    public function index(Request $request)
    {
        $search = $request->search;
        $month = $request->month;
        $year = $request->year;   
        $status = $request->status;

        $services = Service::with(['vehicle.customer', 'mechanic'])
            ->when($search, function ($query, $search) {
                // Dibungkus dengan where() agar orWhere tidak merusak filter bulan dan tahun di bawahnya
                return $query->where(function ($q) use ($search) {
                    $q->whereHas('vehicle', function ($qv) use ($search) {
                        $qv->where('license_plate', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($qc) use ($search) {
                            $qc->where('name', 'like', "%{$search}%");
                        });
                    })
                    // Pencarian murni dari tabel relasi dan keluhan, historical dihapus
                    ->orWhere('complaint', 'like', "%{$search}%");
                });
            })
            // 1. FILTER BULAN
            ->when($month, function ($query, $month) {
                return $query->whereMonth('created_at', $month);
            })
            // 2. FILTER TAHUN
            ->when($year, function ($query, $year) {
                return $query->whereYear('created_at', $year);
            })
            // 3. FILTER STATUS
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('id', 'asc') 
            ->paginate(10);

        // 3. Siapkan array bulan untuk dropdown di tampilan
        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        // 4. Ambil data tahun dari riwayat servis di database
        $years = Service::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
            
        if (empty($years)) {
            $years = [date('Y')];
        }

        return view('admin.services.index', compact('services', 'months', 'years'));
    }

    // Menampilkan Form Tambah Servis Baru
    public function create()
    {
        $vehicles = Vehicle::with('customer')->get();
        $mechanics = Mechanic::all();

        return view('admin.services.create', compact('vehicles', 'mechanics'));
    }

    // Menyimpan Data Servis Baru
    public function store(Request $request)
    {
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

        $mechanic = Mechanic::find($request->mechanic_id);

        // 2. Simpan ke database TANPA data fotokopi (Snapshot) pelanggan/kendaraan
        Service::create([
            'vehicle_id' => $request->vehicle_id,
            'mechanic_id' => $request->mechanic_id,
            'historical_mechanic_name' => $mechanic ? $mechanic->name : null, // Nama mekanik dibiarkan jika tidak diminta dihapus
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

    // Memperbarui Status Servis & Kirim WA
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,finished,canceled,lunas'
        ]);

        $service = Service::with('vehicle.customer')->findOrFail($id);

        if (!$service->vehicle_id) {
            return redirect()->back()->with('error', 'Aksi Ditolak: data kendaraan atau pelanggan terkait telah dihapus dari sistem. Transaksi ini dibekukan dan tidak dapat diselesaikan.');
        }
        
        if (in_array($request->status, ['finished', 'lunas'])) {
            if ($service->total_cost <= 0) {
                return redirect()->back()->with('error', 'Aksi Ditolak: Tidak dapat menyelesaikan servis. Anda wajib memasukkan Biaya Jasa Mekanik atau menambahkan minimal satu Suku Cadang terlebih dahulu!');
            }
        }

        $service->status = $request->status;
        $service->save();

        // Mengambil data langsung dari relasi murni
        $customerName  = $service->vehicle->customer->name ?? 'Pelanggan';
        $customerPhone = $service->vehicle->customer->phone_number ?? '';
        $platNomor     = $service->vehicle->license_plate ?? '-';
        $motor         = $service->vehicle ? $service->vehicle->brand . ' ' . $service->vehicle->model : 'Kendaraan';
        
        if ($request->status == 'finished') {
            $totalBiaya = 'Rp ' . number_format($service->total_cost, 0, ',', '.');

            $pesan = "Halo *{$customerName}*,\n\n";
            $pesan .= "Pengerjaan servis untuk kendaraan bermotor *{$platNomor}* ({$motor}) Anda di MJ MotoPerformance telah *SELESAI* dikerjakan.\n\n";
            $pesan .= "Total Tagihan: *{$totalBiaya}*\n\n";
            $pesan .= "Silakan datang ke bengkel kami untuk melakukan pembayaran dan pengambilan kendaraan.\n\n";
            $pesan .= "Terima kasih telah mempercayakan kendaraan Anda kepada kami!";

            return $this->kirimWhatsApp($customerPhone, $pesan, 'Status Selesai! Notifikasi WhatsApp berhasil dikirim.');
        }
        
        elseif ($request->status == 'lunas') {
            $pesan = "Halo *{$customerName}*,\n\n";
            $pesan .= "Terima kasih atas pembayaran servis kendaraan *{$platNomor}* Anda di MJ MotoPerformance. Tagihan Anda telah dinyatakan *LUNAS*.\n\n";
            $pesan .= "Semoga kendaraan Anda selalu dalam kondisi prima. Hati-hati di jalan dan sampai jumpa di servis berikutnya!";

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

    public function edit($id)
    {
        $service = Service::with('vehicle.customer')->findOrFail($id);

        // Tolak akses jika status sudah Selesai atau Lunas
        if (in_array($service->status, ['finished', 'lunas'])) {
            return redirect()->route('admin.services.index')->with('error', 'Akses Ditolak: Servis telah selesai. Anda tidak dapat mengubah data keluhan atau mekanik lagi.');
        }

        if (!$service->vehicle_id) {
            return redirect()->route('admin.services.index')->with('error', 'Akses Ditolak: data kendaraan untuk transaksi ini sudah dihapus.');
        }

        $vehicles = Vehicle::with('customer')->get();
        $mechanics = Mechanic::all();
        
        return view('admin.services.edit', compact('service', 'vehicles', 'mechanics'));
    }

    // Memperbarui Data Servis dengan Validasi
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
        $service = Service::with('vehicle.customer')->findOrFail($id);

        $rincianBarang = ServiceDetail::where('service_id', $id)->get();
        
        foreach ($rincianBarang as $detail) {
            if ($detail->sparepart_id) {
                Sparepart::where('id', $detail->sparepart_id)->increment('stock', $detail->quantity);
            }
        }
        
        ServiceDetail::where('service_id', $id)->delete();

        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Riwayat antrean servis cacat/batal berhasil dihapus. Seluruh stok suku cadang telah aman dikembalikan ke gudang.');
    }

    public function addSparepart(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        if (in_array($service->status, ['finished', 'lunas'])) {
            return redirect()->back()->with('error', 'Nota sudah ditutup/selesai. Anda tidak dapat menambahkan suku cadang lagi.');
        }

        if (!$service->vehicle_id) {
            return redirect()->back()->with('error', 'Aksi Ditolak: data kendaraan telah dihapus dari sistem. Anda tidak dapat menambahkan barang ke nota cacat ini.');
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

        // DIUBAH: historical_name dihapus dari inputan array create ini
        ServiceDetail::create([
            'service_id' => $service->id,
            'sparepart_id' => $sparepart->id,
            'quantity' => $request->quantity,
            'price' => $sparepart->price, // Harga tetap disimpan (wajib)
            'subtotal' => $subtotal
        ]);

        $sparepart->decrement('stock', $request->quantity);
        $service->increment('total_cost', $subtotal);

        // Jika statusnya masih 'pending', ubah jadi 'processing' karena barang sudah dimasukkan
        if ($service->status === 'pending') {
            $service->update(['status' => 'processing']);
        }

        return redirect()->back()->with('success', 'Suku cadang ditambahkan ke nota dan status transaksi kini diperbarui.');
    }

    // fungsi untuk menghapus suku cadang dari nota servis
    public function removeSparepart($id, $detail_id)
    {
        $service = Service::findOrFail($id);

        if (in_array($service->status, ['finished', 'lunas'])) {
            return redirect()->back()->with('error', 'Aksi Ditolak: Nota sudah ditutup/selesai. Anda tidak dapat menghapus item dari nota ini.');
        }

        if (!$service->vehicle_id) {
            return redirect()->back()->with('error', 'Aksi Ditolak: data kendaraan telah dihapus. Anda tidak dapat memodifikasi nota cacat ini.');
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

    // fungsi untuk memperbarui biaya jasa mekanik
    public function updateServiceCost(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        if (in_array($service->status, ['finished', 'lunas'])) {
            return redirect()->back()->with('error', 'Aksi Ditolak: Anda tidak dapat mengubah biaya jasa karena nota servis telah ditutup.');
        }

        if (!$service->vehicle_id) {
            return redirect()->back()->with('error', 'Aksi Ditolak: data kendaraan telah dihapus. Biaya jasa tidak dapat diubah pada nota cacat ini.');
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