<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Vehicle;
use App\Models\Mechanic;
use Illuminate\Support\Facades\Http; // Wajib diimpor untuk tembak API Fonnte

class ServiceController extends Controller
{
    /**
     * 1. Menampilkan data untuk Tabel React (beserta fungsi Search Anda yang sempurna)
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        // Menggunakan logika pencarian bersarang (Nested) asli milik Anda!
        $services = Service::with(['vehicle.customer', 'mechanic'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('vehicle', function ($q) use ($search) {
                    $q->where('license_plate', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($qc) use ($search) {
                        $qc->where('name', 'like', "%{$search}%");
                    });
                })->orWhere('complaint', 'like', "%{$search}%");
            })
            ->latest() // Mengurutkan dari yang terbaru
            ->get();

        return response()->json([
            'success' => true,
            'data' => $services
        ], 200);
    }

    /**
     * 2. Mengubah Status & Memicu WhatsApp Fonnte
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,finished,canceled'
        ]);

        $service = Service::with('vehicle.customer')->findOrFail($id);
        $service->status = $request->status;
        $service->save();

        // LOGIKA WHATSAPP GATEWAY (API FONNTE) ASLI MILIK ANDA
        if ($request->status == 'finished') {
            
            $customerName = $service->vehicle->customer->name;
            $customerPhone = $service->vehicle->customer->phone_number;
            $platNomor = $service->vehicle->license_plate;
            $motor = $service->vehicle->brand . ' ' . $service->vehicle->model;
            $totalBiaya = 'Rp ' . number_format($service->total_cost, 0, ',', '.');

            $pesan = "Halo *{$customerName}*,\n\n";
            $pesan .= "Pengerjaan servis untuk kendaraan bermotor *{$platNomor}* ({$motor}) Anda di MJ MotoPerformance telah *SELESAI* dikerjakan.\n\n";
            $pesan .= "Total Tagihan: *{$totalBiaya}*\n\n";
            $pesan .= "Silakan datang ke bengkel kami untuk melakukan pengecekan dan pengambilan kendaraan.\n\n";
            $pesan .= "Terima kasih telah mempercayakan kendaraan Anda kepada kami! 🙏";

            try {
                $response = Http::withHeaders([
                    'Authorization' => env('FONNTE_TOKEN')
                ])->post('https://api.fonnte.com/send', [
                    'target' => $customerPhone,
                    'message' => $pesan,
                    'countryCode' => '62',
                ]);

                if ($response->successful()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Status selesai! Notifikasi WhatsApp berhasil dikirim ke pelanggan.'
                    ]);
                } else {
                    return response()->json([
                        'success' => true,
                        'message' => 'Status selesai, namun gagal mengirim WA. Pastikan Device Fonnte terkoneksi.'
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => true,
                    'message' => 'Status selesai, tapi terjadi error sistem WA: ' . $e->getMessage()
                ]);
            }
        }

        // Jika bukan finished, beri respon sukses biasa
        return response()->json([
            'success' => true,
            'message' => 'Status pengerjaan kendaraan berhasil diperbarui!'
        ]);
    }

    /**
     * 3. Menghapus Data
     */
    public function destroy($id)
    {
        Service::findOrFail($id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'Riwayat servis berhasil dihapus!'
        ]);
    }


    /**
     * Menampilkan Detail Nota Servis beserta daftar Suku Cadang yang tersedia
     */
    public function show($id)
    {
        // Ambil data servis berserta detail relasinya
        $service = Service::with(['vehicle.customer', 'mechanic', 'details.sparepart'])->findOrFail($id);
        
        // Ambil daftar suku cadang yang stoknya lebih dari 0 untuk dropdown
        // Catatan: Pastikan Anda sudah mengimpor App\Models\Sparepart di bagian atas file
        $spareparts = \App\Models\Sparepart::where('stock', '>', 0)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'service' => $service,
                'spareparts' => $spareparts
            ]
        ]);
    }

    /**
     * Menambahkan Suku Cadang ke Nota
     */
    public function addSparepart(Request $request, $id)
    {
        $request->validate([
            'sparepart_id' => 'required|exists:spareparts,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $service = Service::findOrFail($id);
        $sparepart = \App\Models\Sparepart::findOrFail($request->sparepart_id);

        if ($sparepart->stock < $request->quantity) {
            return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi!'], 400);
        }

        $subtotal = $sparepart->price * $request->quantity;

        // Pastikan Anda sudah mengimpor App\Models\ServiceDetail di atas
        \App\Models\ServiceDetail::create([
            'service_id' => $service->id,
            'sparepart_id' => $sparepart->id,
            'quantity' => $request->quantity,
            'price' => $sparepart->price,
            'subtotal' => $subtotal
        ]);

        $sparepart->decrement('stock', $request->quantity);
        $service->increment('total_cost', $subtotal);

        return response()->json(['success' => true, 'message' => 'Suku cadang ditambahkan.']);
    }

    /**
     * Menghapus Suku Cadang dari Nota
     */
    public function removeSparepart($id, $detail_id)
    {
        $service = Service::findOrFail($id);
        $detail = \App\Models\ServiceDetail::findOrFail($detail_id);
        $sparepart = \App\Models\Sparepart::findOrFail($detail->sparepart_id);

        $sparepart->increment('stock', $detail->quantity);
        $service->decrement('total_cost', $detail->subtotal);
        $detail->delete();

        return response()->json(['success' => true, 'message' => 'Suku cadang dihapus.']);
    }

    /**
     * Memperbarui Biaya Jasa Mekanik
     */
    public function updateCost(Request $request, $id)
    {
        $request->validate(['service_cost' => 'required|numeric|min:0']);

        $service = Service::findOrFail($id);
        $oldCost = $service->service_cost;
        $newCost = $request->service_cost;

        $service->service_cost = $newCost;
        $service->total_cost = ($service->total_cost - $oldCost) + $newCost;
        $service->save();

        return response()->json(['success' => true, 'message' => 'Biaya jasa diperbarui.']);
    }

    /**
     * Mengambil data untuk Dropdown Form Tambah Servis
     */
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
     * Menyimpan data antrean servis baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'mechanic_id' => 'required|exists:mechanics,id',
            'complaint' => 'required|string',
        ]);

        \App\Models\Service::create([
            'vehicle_id' => $request->vehicle_id,
            'mechanic_id' => $request->mechanic_id,
            'complaint' => $request->complaint,
            'status' => 'pending', // Otomatis masuk antrean
            'service_cost' => 0,
            'total_cost' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Antrean kendaraan berhasil ditambahkan!'
        ]);
    }


    /**
     * Menyimpan perubahan data Servis (Edit) dari Mobile
     */
    public function update(Request $request, $id)
    {
        // Validasi data yang masuk
        $request->validate([
            'complaint' => 'required|string',
            // mechanic_id bisa kosong (null) jika admin belum menentukan
        ]);

        // Cari data servisnya
        $service = Service::findOrFail($id);

        // Timpa data lama dengan data baru
        $service->mechanic_id = $request->mechanic_id;
        $service->complaint = $request->complaint;
        $service->save();

        // Kembalikan jawaban sukses ke Mobile
        return response()->json([
            'success' => true,
            'message' => 'Informasi servis berhasil diperbarui!'
        ]);
    }
}