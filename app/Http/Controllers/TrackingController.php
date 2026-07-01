<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;

class TrackingController extends Controller
{
    // Menampilkan Dasbor Pelanggan
    public function index()
    {
        // 1. UBAH: Ambil data user yang login MURNI dari guard 'customer'
        // Karena yang login saat ini adalah entitas Customer itu sendiri
        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            return redirect('/login')->with('error', 'Sesi Anda telah habis. Silakan login kembali.');
        }

        // 2. Cari riwayat servis berdasarkan ID kendaraan milik pelanggan tersebut
        $services = Service::whereHas('vehicle', function($query) use ($customer) {
            $query->where('customer_id', $customer->id);
        })->with(['vehicle', 'mechanic', 'details.sparepart'])
        ->latest()
        ->get();

        return view('frontend.tracking', compact('customer', 'services'));
    }
}