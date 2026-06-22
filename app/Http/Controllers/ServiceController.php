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

        $services = Service::with(['vehicle.customer', 'mechanic'])
            ->when($search, function ($query, $search) {
                // Dibungkus dengan where() agar orWhere 
                // tidak membocorkan/merusak filter bulan dan tahun di bawahnya
                return $query->where(function ($q) use ($search) {
                    $q->whereHas('vehicle', function ($qv) use ($search) {
                        $qv->where('license_plate', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($qc) use ($search) {
                            $qc->where('name', 'like', "%{$search}%");
                        });
                    })
                    // Pencarian di kolom snapshot
                    ->orWhere('historical_license_plate', 'like', "%{$search}%")
                    ->orWhere('historical_customer_name', 'like', "%{$search}%")
                    ->orWhere('complaint', 'like', "%{$search}%");
                });
            })
            // 1. TAMBAHAN FILTER BULAN 
            ->when($month, function ($query, $month) {
                return $query->whereMonth('created_at', $month);
            })
            // 2. TAMBAHAN FILTER TAHUN
            ->when($year, function ($query, $year) {
                return $query->whereYear('created_at', $year);
            })
            ->orderBy('id', 'asc') 
            ->paginate(10);

        // 3. Siapkan array bulan untuk dropdown di tampilan (View)
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
            
            // PROSES SNAPSHOT: Mengunci riwayat data saat ini 
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

        // Ambil data dari kolom historis, jika kosong baru ambil dari relasi aktif
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

        // PENGAMANAN: Jika data master kendaraan sudah terhapus (vehicle_id null), tolak edit agar tidak merusak relasi dropdown
        if (!$service->vehicle_id) {
            return redirect()->route('admin.services.index')->with('error', 'Akses Ditolak: Master data kendaraan untuk transaksi ini sudah dihapus.');
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
        $service = Service::findOrFail($id);

        // BLOKIR PENGHAPUSAN JIKA SUDAH LUNAS ATAU SELESAI
        if (in_array($service->status, ['finished', 'lunas'])) {
            return redirect()->back()->with('error', 'Aksi Ditolak: Nota yang sudah Selesai atau Lunas telah masuk ke dalam buku besar pendapatan dan tidak boleh dihapus secara fisik demi integritas laporan keuangan.');
        }

        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Riwayat antrean servis yang belum diproses berhasil dibatalkan dan dihapus.');
    }


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

        // if ($service->status === 'pending') {
        //     $service->update(['status' => 'processing']);

        //     // Data snapshot pelanggan
        //     $customerName  = $service->historical_customer_name ?? ($service->vehicle->customer->name ?? 'Pelanggan');
        //     $customerPhone = $service->historical_customer_phone ?? ($service->vehicle->customer->phone_number ?? '');
        //     $platNomor     = $service->historical_license_plate ?? ($service->vehicle->license_plate ?? '-');
        //     $motor         = $service->historical_vehicle_motor ?? ($service->vehicle ? $service->vehicle->brand . ' ' . $service->vehicle->model : 'Kendaraan');

        //     $pesan = "Halo *{$customerName}*,\n\n";
        //     $pesan .= "Kendaraan bermotor *{$platNomor}* ({$motor}) Anda saat ini sedang *MULAI DIKERJAKAN* oleh mekanik.\n\n";
        //     $pesan .= "Kami akan menginformasikan kembali jika pengerjaan telah selesai.\n\n";
        //     $pesan .= "Terima kasih atas kesabaran Anda!";

        //     $this->kirimWhatsApp($customerPhone, $pesan, 'Sukses');
        // }

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