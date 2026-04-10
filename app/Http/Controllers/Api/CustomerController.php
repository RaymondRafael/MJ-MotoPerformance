<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Service; 

class CustomerController extends Controller
{
    public function myGarage(Request $request)
    {
        $user = $request->user();
        
        $customer = Customer::where('user_id', $user->id)->first();

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Profil pelanggan tidak ditemukan.'], 404);
        }

        // --- 1. TANGKAP FILTER DARI MOBILE ---
        $month = $request->query('month');
        $year = $request->query('year');

        // --- 2. BUAT PONDASI QUERY (Untuk Customer Ini Saja) ---
        $baseQuery = Service::with(['vehicle', 'mechanic', 'details.sparepart'])
            ->whereHas('vehicle', function ($query) use ($customer) {
                $query->where('customer_id', $customer->id);
            });

        // --- 3. AMBIL DATA AKTIF (WAJIB TAMPIL, Mengabaikan Filter Waktu) ---
        $activeServices = (clone $baseQuery)
            ->whereIn('status', ['pending', 'processing'])
            ->orderBy('created_at', 'desc')
            ->get();

        // --- 4. AMBIL DATA RIWAYAT (DITERAPKAN FILTER WAKTU) ---
        $historyQuery = (clone $baseQuery)->where('status', 'finished');
        
        // Jika ada filter bulan & tahun, terapkan ke Riwayat
        if ($month && $year) {
            $historyQuery->whereMonth('created_at', $month)
                         ->whereYear('created_at', $year);
        }

        $historyServices = $historyQuery->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'customer_name' => $customer->name,
                
                // MAPPING DATA AKTIF
                'active' => $activeServices->map(function($service) {
                    return [
                        'id' => $service->id,
                        'status' => $service->status,
                        'complaint' => $service->complaint,
                        'total_cost' => (float) ($service->total_cost ?? 0),
                        'vehicle' => [
                            'plat' => $service->vehicle?->license_plate ?? '-',
                            'merek' => ($service->vehicle?->brand ?? '') . ' ' . ($service->vehicle?->model ?? ''),
                        ]
                    ];
                }),
                
                // MAPPING DATA RIWAYAT
                'history' => $historyServices->map(function($service) {
                    return [
                        'id' => $service->id,
                        'tanggal' => $service->created_at ? $service->created_at->format('d M Y') : '-',
                        'keluhan' => $service->complaint,
                        'mekanik' => $service->mechanic?->name ?? 'Belum ada',
                        'biaya' => (float) ($service->total_cost ?? 0),
                        
                        'jasa_servis' => (float) ($service->service_cost ?? 0),
                        'rincian_suku_cadang' => $service->details->map(function($detail) {
                            return [
                                'nama' => $detail->sparepart?->name ?? 'Suku Cadang',
                                'qty' => $detail->quantity,
                                'subtotal' => (float) ($detail->subtotal ?? 0),
                            ];
                        }),
                        
                        'vehicle' => [
                            'plat' => $service->vehicle?->license_plate ?? '-',
                            'merek' => ($service->vehicle?->brand ?? '') . ' ' . ($service->vehicle?->model ?? ''),
                        ]
                    ];
                })
            ]
        ], 200);
    }
}