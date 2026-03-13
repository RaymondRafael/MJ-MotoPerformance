<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Customer;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Dapatkan input bulan & tahun. Jika kosong, gunakan bulan & tahun semasa.
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $selectedYear = $request->input('year', Carbon::now()->year);

        // 2. Pendapatan (Hanya yang berstatus 'finished' pada bulan & tahun tersebut)
        $pendapatan = Service::where('status', 'finished')
                            ->whereMonth('created_at', $selectedMonth)
                            ->whereYear('created_at', $selectedYear)
                            ->sum('total_cost');

        // 3. Antrean Servis (Yang masuk pada bulan & tahun tersebut)
        $antreanAktif = Service::whereIn('status', ['pending', 'processing'])
                            ->whereMonth('created_at', $selectedMonth)
                            ->whereYear('created_at', $selectedYear)
                            ->count();

        // 4. Servis Selesai (Yang disiapkan pada bulan & tahun tersebut)
        $selesaiPeriode = Service::where('status', 'finished')
                                ->whereMonth('updated_at', $selectedMonth)
                                ->whereYear('updated_at', $selectedYear)
                                ->count();

        // 5. Pelanggan Baru (Yang mendaftar pada bulan & tahun tersebut)
        $pelangganBaru = Customer::whereMonth('created_at', $selectedMonth)
                                ->whereYear('created_at', $selectedYear)
                                ->count();

        // 6. 5 Antrean Teratas (Ini biarkan secara global untuk memantau bengkel secara live)
        $antreanTerbaru = Service::with(['vehicle.customer'])
                                ->whereIn('status', ['pending', 'processing'])
                                ->orderBy('created_at', 'asc')
                                ->take(5)
                                ->get();

        return view('admin.dashboard', compact(
            'pendapatan',
            'antreanAktif',
            'selesaiPeriode',
            'pelangganBaru',
            'antreanTerbaru',
            'selectedMonth',
            'selectedYear'
        ));
    }
}