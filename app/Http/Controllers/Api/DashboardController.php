<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Customer;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Dapatkan input bulan & tahun dari React
        $selectedMonth = $request->query('month', Carbon::now()->month);
        $selectedYear = $request->query('year', Carbon::now()->year);

        // 2. Pendapatan (Hitung yang statusnya finished ATAU lunas)
        // 'updated_at' agar uang masuk dihitung berdasarkan bulan saat nota dilunasi
        $pendapatan = Service::whereIn('status', ['finished', 'lunas'])
                            ->whereMonth('updated_at', $selectedMonth)
                            ->whereYear('updated_at', $selectedYear)
                            ->sum('total_cost');

        // 3. Antrean Servis (Yang masuk pada bulan & tahun tersebut)
        $antreanAktif = Service::whereIn('status', ['pending', 'processing'])
                            ->whereMonth('created_at', $selectedMonth)
                            ->whereYear('created_at', $selectedYear)
                            ->count();

        // 4. Servis Selesai (Hitung yang statusnya finished ATAU lunas)
        $selesaiPeriode = Service::whereIn('status', ['finished', 'lunas'])
                                ->whereMonth('updated_at', $selectedMonth)
                                ->whereYear('updated_at', $selectedYear)
                                ->count();

        // 5. Pelanggan Baru
        $pelangganBaru = Customer::whereMonth('created_at', $selectedMonth)
                                ->whereYear('created_at', $selectedYear)
                                ->count();

        // 6. 5 Antrean Teratas 
        $antreanTerbaru = Service::with(['vehicle.customer'])
                                ->whereIn('status', ['pending', 'processing'])
                                ->orderBy('created_at', 'asc')
                                ->take(5)
                                ->get()
                                ->map(function ($item) {
                                    return [
                                        'id' => $item->id,
                                        'waktu' => $item->created_at->diffForHumans(),
                                        'plat' => $item->vehicle->license_plate ?? 'Tidak Diketahui',
                                        'pelanggan' => $item->vehicle->customer->name ?? 'Tidak Diketahui',
                                        'status' => $item->status,
                                    ];
                                });

        // 7. Kembalikan semua data
        return response()->json([
            'success' => true,
            'data' => [
                'pendapatan' => $pendapatan,
                'antreanAktif' => $antreanAktif,
                'selesaiPeriode' => $selesaiPeriode,
                'pelangganBaru' => $pelangganBaru,
                'antreanTerbaru' => $antreanTerbaru
            ]
        ], 200);
    }
}