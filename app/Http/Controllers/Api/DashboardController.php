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

        // 2. Pendapatan (Menggunakan 'total_cost' sesuai database Anda)
        $pendapatan = Service::where('status', 'finished')
                            ->whereMonth('created_at', $selectedMonth)
                            ->whereYear('created_at', $selectedYear)
                            ->sum('total_cost');

        // 3. Antrean Servis (Yang masuk pada bulan & tahun tersebut)
        $antreanAktif = Service::whereIn('status', ['pending', 'processing'])
                            ->whereMonth('created_at', $selectedMonth)
                            ->whereYear('created_at', $selectedYear)
                            ->count();

        // 4. Servis Selesai (Menggunakan 'updated_at' sesuai logika Anda)
        $selesaiPeriode = Service::where('status', 'finished')
                                ->whereMonth('updated_at', $selectedMonth)
                                ->whereYear('updated_at', $selectedYear)
                                ->count();

        // 5. Pelanggan Baru
        $pelangganBaru = Customer::whereMonth('created_at', $selectedMonth)
                                ->whereYear('created_at', $selectedYear)
                                ->count();

        // 6. 5 Antrean Teratas (Di-map agar formatnya persis dengan yang diminta React)
        $antreanTerbaru = Service::with(['vehicle.customer'])
                                ->whereIn('status', ['pending', 'processing'])
                                ->orderBy('created_at', 'asc')
                                ->take(5)
                                ->get()
                                ->map(function ($item) {
                                    return [
                                        'id' => $item->id,
                                        'waktu' => $item->created_at->diffForHumans(),
                                        // Hati-hati: pastikan nama kolom di DB Anda benar 'license_plate'
                                        'plat' => $item->vehicle->license_plate ?? 'Tidak Diketahui',
                                        'pelanggan' => $item->vehicle->customer->name ?? 'Tidak Diketahui',
                                        'status' => $item->status,
                                    ];
                                });

        // 7. Kembalikan semua data dalam format JSON
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