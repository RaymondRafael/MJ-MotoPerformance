<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Service; 

class CustomerController extends Controller
{
    // Menampilkan data pelanggan beserta riwayat servis untuk tampilan "Garasi Saya" di aplikasi mobile
    public function myGarage(Request $request)
    {
        $user = $request->user();
        
        $customer = Customer::where('user_id', $user->id)->first();

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Profil pelanggan tidak ditemukan.'], 404);
        }

        // 1. untuk filter bulan & tahun pada riwayat
        $month = $request->query('month');
        $year = $request->query('year');

        // 2. Query untuk mengambil semua servis pelanggan, baik aktif maupun riwayat
        $baseQuery = Service::with(['vehicle', 'mechanic', 'details.sparepart'])
            ->whereHas('vehicle', function ($query) use ($customer) {
                $query->where('customer_id', $customer->id);
            });

        // 3. Masukkan 'finished' ke Active Services agar sinkron dengan Web Tracking
        $activeServices = (clone $baseQuery)
            ->whereIn('status', ['pending', 'processing', 'finished']) 
            ->orderBy('created_at', 'desc')
            ->get();

        // 4. History hanya memuat yang sudah 'lunas' 
        $historyQuery = (clone $baseQuery)->whereIn('status', ['lunas', 'completed', 'paid']);
        
        // Jika ada filter bulan & tahun, terapkan ke Riwayat
        if ($month && $year) {
            $historyQuery->whereMonth('created_at', $month)
                        ->whereYear('created_at', $year);
        }

        $historyServices = $historyQuery->orderBy('updated_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'customer_name' => $customer->name,
                
                // DATA SERVIS AKTIF
                'active' => $activeServices->map(function($service) {
                    return [
                        'id' => $service->id,
                        'status' => $service->status,
                        'complaint' => $service->complaint,
                        'total_cost' => (float) ($service->total_cost ?? 0),
                        'jasa_servis' => (float) ($service->service_cost ?? 0),
                        
                        // Menampilkan data mekanik bertugas secara di aplikasi mobile
                        'mekanik' => $service->mechanic?->name, 
                        'historical_mechanic_name' => $service->historical_mechanic_name,
                        
                        // Menyertakan rincian barang agar aplikasi mobile bisa menampilkan detail belanjaan saat diservis
                        'rincian_suku_cadang' => $service->details->map(function($detail) {
                            return [
                                'nama' => $detail->sparepart?->name, 
                                'historical_name' => $detail->historical_name, 
                                'qty' => $detail->quantity,
                                'subtotal' => (float) ($detail->subtotal ?? 0),
                            ];
                        }),
                        'vehicle' => [
                            'plat' => $service->vehicle?->license_plate ?? '-',
                            'merek' => ($service->vehicle?->brand ?? '') . ' ' . ($service->vehicle?->model ?? ''),
                        ]
                    ];
                }),
                
                // DATA RIWAYAT SERVIS
                'history' => $historyServices->map(function($service) {
                    return [
                        'id' => $service->id,
                        'status' => $service->status, 
                        'tanggal' => $service->created_at ? $service->created_at->format('d M Y') : '-',
                        'keluhan' => $service->complaint,
                        'mekanik' => $service->mechanic?->name, 
                        'historical_mechanic_name' => $service->historical_mechanic_name,
                        'biaya' => (float) ($service->total_cost ?? 0),
                        'jasa_servis' => (float) ($service->service_cost ?? 0),
                        'rincian_suku_cadang' => $service->details->map(function($detail) {
                            return [
                                'nama' => $detail->sparepart?->name, 
                                'historical_name' => $detail->historical_name, 
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