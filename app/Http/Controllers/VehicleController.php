<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Customer;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        // Mencari berdasarkan plat nomor atau nama pemilik
        $vehicles = \App\Models\Vehicle::with('customer')
            ->when($search, function ($query, $search) {
                return $query->where('license_plate', 'like', "%{$search}%")
                            ->orWhereHas('customer', function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%");
                            });
            })
            ->latest()
            ->get();

        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $customers = Customer::all(); // Mengambil semua pelanggan untuk dropdown
        return view('admin.vehicles.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'license_plate' => 'required|unique:vehicles,license_plate',
            'brand' => 'required',
            'model' => 'required'
        ]);
        Vehicle::create($request->all());
        return redirect()->route('admin.vehicles.index')->with('success', 'Data kendaraan berhasil ditambahkan.');
    }

    public function edit(Vehicle $vehicle)
    {
        $customers = Customer::all();
        return view('admin.vehicles.edit', compact('vehicle', 'customers'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'license_plate' => 'required|unique:vehicles,license_plate,' . $vehicle->id,
            'brand' => 'required',
            'model' => 'required'
        ]);
        $vehicle->update($request->all());
        return redirect()->route('admin.vehicles.index')->with('success', 'Data kendaraan berhasil diperbarui.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('admin.vehicles.index')->with('success', 'Data kendaraan berhasil dihapus.');
    }
}