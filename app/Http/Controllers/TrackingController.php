<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;

class TrackingController extends Controller
{
    // Menampilkan Dasbor Pribadi Pelanggan
    public function index()
    {
        // 1. Ambil data profil pelanggan yang sedang login
        $customer = Auth::user()->customer;

        // 2. Jika user login tapi tidak punya profil customer (misal Admin nyasar), lempar kembali
        if (!$customer) {
            return redirect('/')->with('error', 'Akses ditolak. Anda bukan pelanggan.');
        }

        // 3. Ambil semua riwayat servis dari semua kendaraan milik pelanggan ini
        $services = Service::whereHas('vehicle', function($query) use ($customer) {
            $query->where('customer_id', $customer->id);
        })->with(['vehicle', 'mechanic', 'details.sparepart'])
        ->latest()
        ->get();

        return view('frontend.tracking', compact('customer', 'services'));
    }
}