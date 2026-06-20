<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Vehicle;
use App\Models\Mechanic;
use App\Models\Sparepart;
use App\Models\ServiceDetail;
use Illuminate\Support\Facades\Http;

class ServiceController extends Controller
{
    /**
     * 1. Menampilkan data untuk Tabel React (Mendukung Pencarian Snapshot)
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $services = Service::with(['vehicle.customer', 'mechanic'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('vehicle', function ($q) use ($search) {
                    $q->where('license_plate', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($qc) use ($search) {
                        $qc->where('name', 'like', "%{$search}%");
                    });
                })
                // MENYAMAKAN WEB: Cari juga di kolom snapshot jika master data sudah dihapus
                ->orWhere('historical_license_plate', 'like', "%{$search}%")
                ->orWhere('historical_customer_name', 'like', "%{$search}%")
                ->orWhere('complaint', 'like', "%{$search}%");
            })
            ->latest() 
            ->get();

        return response()->json([
            'success' => true,
            'data' => $services
        ], 200);
    }

    /**
     * 2. Mengubah Status & Memicu WhatsApp Fonnte (Menggunakan Snapshot Cadangan)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,finished,canceled,lunas'
        ]);

        $service = Service::findOrFail($id);

        // --- PENGAMANAN ALUR: Blokir Loncat Status ---
        if ($request->status === 'finished' && $service->status === 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Aksi Ditolak: Kendaraan belum diproses. Silakan tambahkan biaya jasa atau suku cadang terlebih dahulu.'
            ], 403);
        }

        $service->status = $request->status;
        $service->save();

        // MENYAMAKAN WEB: Ambil data dari kolom snapshot (anti-crash jika master data dihapus)
        $customerName  = $service->historical_customer_name ?? ($service->vehicle->customer->name ?? 'Pelanggan');
        $customerPhone = $service->historical_customer_phone ?? ($service->vehicle->customer->phone_number ?? '');
        $platNomor     = $service->historical_license_plate ?? ($service->vehicle->license_plate ?? '-');
        $motor         = $service->historical_vehicle_motor ?? ($service->vehicle ? $service->vehicle->brand . ' ' . $service->vehicle->model : 'Kendaraan');
        
        if ($request->status == 'finished') {
            $totalBiaya = 'Rp ' . number_format($service->total_cost, 0, ',', '.');

            $pesan = "Halo *{$customerName}*,\n\n";
            $pesan .= "Pengerjaan servis untuk kendaraan bermotor *{$platNomor}* ({$motor}) Anda di MJ MotoPerformance telah *SELESAI* dikerjakan.\n\n";
            $pesan .= "Total Tagihan: *{$totalBiaya}*\n\n";
            $pesan .= "Silakan datang ke bengkel kami untuk melakukan pengecekan dan pengambilan kendaraan.\n\n";
            $pesan .= "Terima kasih telah mempercayakan kendaraan Anda kepada kami! 🙏";

            return $this->kirimWhatsApp($customerPhone, $pesan, 'Status selesai! Notifikasi WhatsApp berhasil dikirim ke pelanggan.');
        }
        
        elseif ($request->status == 'lunas') {
            $pesan = "Halo *{$customerName}*,\n\n";
            $pesan .= "Terima kasih atas pembayaran servis kendaraan *{$platNomor}* Anda di MJ MotoPerformance. Tagihan Anda telah dinyatakan *LUNAS*.\n\n";
            $pesan .= "Semoga kendaraan Anda selalu dalam kondisi prima. Hati-hati di jalan dan sampai jumpa di servis berikutnya! 🏍️💨";

            return $this->kirimWhatsApp($customerPhone, $pesan, 'Pembayaran Lunas! Ucapan terima kasih berhasil dikirim.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Status pengerjaan kendaraan berhasil diperbarui!'
        ]);
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
                return response()->json([
                    'success' => true,
                    'message' => $successMessage
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Status tersimpan, namun gagal mengirim WA. Pastikan Device Fonnte terkoneksi.'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => true,
                'message' => 'Status tersimpan, tapi terjadi error sistem WA: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 3. Menghapus Data (Menerapkan Restriksi Integritas Finansial)
     */
    public function destroy($id)
    {
        $service = Service::findOrFail($id);

        // MENYAMAKAN WEB: Kunci nota yang sudah bernilai uang agar kasir/admin tidak bisa manipulasi
        if (in_array($service->status, ['finished', 'lunas'])) {
            return response()->json([
                'success' => false,
                'message' => 'Aksi Ditolak: Transaksi yang telah selesai atau lunas terkunci di buku besar keuangan dan dilarang dihapus.'
            ], 403);
        }

        $service->delete();
        return response()->json([
            'success' => true,
            'message' => 'Riwayat antrean servis berhasil dibatalkan dan dihapus!'
        ]);
    }

    public function show($id)
    {
        $service = Service::with(['vehicle.customer', 'mechanic', 'details.sparepart'])->findOrFail($id);
        $spareparts = Sparepart::where('stock', '>', 0)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'service' => $service,
                'spareparts' => $spareparts
            ]
        ]);
    }

    public function create()
    {
        $vehicles = Vehicle::with('customer')->get();
        $mechanics = Mechanic::all();

        return response()->json([
            'success' => true,
            'data' => [
                'vehicles' => $vehicles,
                'mechanics' => $mechanics
            ]
        ]);
    }

    /**
     * 4. Menyimpan Data Servis Baru (Proses Perekaman Snapshot Data)
     */
    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'mechanic_id' => 'required|exists:mechanics,id',
            'complaint' => 'required|string',
        ]);

        // 1. Dapatkan data fisik kendaraan dan identitas pelanggan
        $vehicle = Vehicle::with('customer')->findOrFail($request->vehicle_id);
        $mechanic = Mechanic::find($request->mechanic_id);

        // 2. Terapkan pemulihan data pasif (Snapshot)
        $service = Service::create([
            'vehicle_id' => $request->vehicle_id,
            'mechanic_id' => $request->mechanic_id,
            
            // PROSES SNAPSHOT: Kunci wujud data asli detik ini juga
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

        return response()->json([
            'success' => true,
            'message' => 'Antrean kendaraan berhasil ditambahkan!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        if (in_array($service->status, ['finished', 'lunas'])) {
            return response()->json(['success' => false, 'message' => 'Aksi Ditolak: Transaksi servis ini telah ditutup secara permanen.'], 403);
        }

        $request->validate([
            'complaint' => 'required|string',
            'mechanic_id' => 'nullable|exists:mechanics,id',
        ]);

        // Proteksi jika kendaraan master terhapus
        if (!$service->vehicle_id) {
            return response()->json(['success' => false, 'message' => 'Aksi Ditolak: Master data kendaraan transaksi ini sudah dihapus.'], 403);
        }

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

        return response()->json([
            'success' => true,
            'message' => 'Informasi servis berhasil diperbarui!'
        ]);
    }

    public function addSparepart(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        if (in_array($service->status, ['finished', 'lunas'])) {
            return response()->json(['success' => false, 'message' => 'Nota sudah ditutup/selesai. Anda tidak dapat menambahkan suku cadang lagi.'], 403);
        }

        $request->validate([
            'sparepart_id' => 'required|exists:spareparts,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $sparepart = Sparepart::findOrFail($request->sparepart_id);

        if ($sparepart->stock < $request->quantity) {
            return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi!'], 400);
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

        return response()->json(['success' => true, 'message' => 'Suku cadang ditambahkan.']);
    }

    public function removeSparepart($id, $detail_id)
    {
        $service = Service::findOrFail($id);

        if (in_array($service->status, ['finished', 'lunas'])) {
            return response()->json(['success' => false, 'message' => 'Aksi Ditolak: Nota sudah ditutup. Anda tidak dapat menghapus item ini.'], 403);
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

        return response()->json(['success' => true, 'message' => 'Suku cadang dihapus.']);
    }

    public function updateCost(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        if (in_array($service->status, ['finished', 'lunas'])) {
            return response()->json(['success' => false, 'message' => 'Aksi Ditolak: Anda tidak dapat mengubah biaya jasa karena nota servis telah ditutup.'], 403);
        }

        $request->validate(['service_cost' => 'required|numeric|min:0']);

        $oldCost = $service->service_cost;
        $newCost = $request->service_cost;

        $service->service_cost = $newCost;
        $service->total_cost = ($service->total_cost - $oldCost) + $newCost;
        
        if ($service->status === 'pending' && $newCost > 0) {
            $service->status = 'processing';
        }
        
        $service->save();

        return response()->json(['success' => true, 'message' => 'Biaya jasa diperbarui.']);
    }
}