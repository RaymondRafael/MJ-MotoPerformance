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
        $customer = Auth::user()->customer;

        if (!$customer) {
            return redirect('/')->with('error', 'Akses ditolak. Anda bukan pelanggan.');
        }

        $services = Service::whereHas('vehicle', function($query) use ($customer) {
            $query->where('customer_id', $customer->id);
        })->with(['vehicle', 'mechanic', 'details.sparepart'])
        ->latest()
        ->get();

        return view('frontend.tracking', compact('customer', 'services'));
    }
}