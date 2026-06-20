<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Customer;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    // Menampilkan Daftar Kendaraan dengan Fitur Pencarian
    public function index(Request $request)
    {
        $search = $request->search;

        $vehicles = \App\Models\Vehicle::with('customer')
            ->when($search, function ($query, $search) {
                return $query->where('license_plate', 'like', "%{$search}%")
                            ->orWhereHas('customer', function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%");
                            });
            })
            ->latest()
            ->paginate(10);

        return view('admin.vehicles.index', compact('vehicles'));
    }

    // Menampilkan Form Tambah Kendaraan Baru
    public function create()
    {
        $customers = Customer::all(); 
        return view('admin.vehicles.create', compact('customers'));
    }

    // Menyimpan Data Kendaraan Baru
    public function store(Request $request)
    {
        // Aturan Validasi Input untuk Menambahkan Kendaraan Baru
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'license_plate' => [
                'required',
                'regex:/^[a-zA-Z]{1,2}\s*\d{1,4}\s*[a-zA-Z]{0,3}$/',
                'unique:vehicles,license_plate'
            ],
            'brand' => 'required',
            'model' => 'required',
            'color' => 'required'
        ], [
            'customer_id.required' => 'Gagal: Anda harus memilih nama pemilik kendaraan dari daftar pelanggan.',
            'customer_id.exists' => 'Gagal: Data pelanggan yang dipilih tidak valid atau telah dihapus.',
            'license_plate.required' => 'Gagal: Plat nomor kendaraan wajib diisi.',
            'license_plate.regex' => 'Gagal: Format plat nomor tidak valid. Gunakan huruf dan angka tanpa simbol (Contoh: D 1234 ABC).',
            'license_plate.unique' => 'Gagal: Plat nomor kendaraan ini sudah terdaftar di sistem kami.',
            'brand.required' => 'Gagal: Merek kendaraan wajib diisi (Contoh: Honda).',
            'model.required' => 'Gagal: Model kendaraan wajib diisi (Contoh: Vario 150).',
            'color.required' => 'Gagal: Warna kendaraan wajib diisi (Contoh: Hitam Matte).',
        ]);
        
        $data = $request->all();
        $data['license_plate'] = strtoupper($request->license_plate);

        Vehicle::create($data);
        return redirect()->route('admin.vehicles.index')->with('success', 'Data kendaraan berhasil ditambahkan.');
    }

    // Menampilkan Form Edit Data Kendaraan
    public function edit(Vehicle $vehicle)
    {
        $customers = Customer::all();
        return view('admin.vehicles.edit', compact('vehicle', 'customers'));
    }

    // Update Data Kendaraan
    public function update(Request $request, Vehicle $vehicle)
    {
        // Aturan Validasi Input untuk Mengubah Data Kendaraan
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'license_plate' => [
                'required',
                'regex:/^[a-zA-Z]{1,2}\s*\d{1,4}\s*[a-zA-Z]{0,3}$/',
                'unique:vehicles,license_plate,' . $vehicle->id
            ],
            'brand' => 'required',
            'model' => 'required',
            'color' => 'required'
        ], [
            'customer_id.required' => 'Gagal: Anda harus memilih nama pemilik kendaraan.',
            'customer_id.exists' => 'Gagal: Data pelanggan yang dipilih tidak valid atau telah dihapus.',
            'license_plate.required' => 'Gagal: Plat nomor kendaraan wajib diisi.',
            'license_plate.regex' => 'Gagal: Format plat nomor tidak valid. Gunakan huruf dan angka tanpa simbol (Contoh: B 123 AA).',
            'license_plate.unique' => 'Gagal: Plat nomor ini sudah terdaftar di kendaraan lain.',
            'brand.required' => 'Gagal: Merek kendaraan wajib diisi.',
            'model.required' => 'Gagal: Model kendaraan wajib diisi.',
            'color.required' => 'Gagal: Warna kendaraan wajib diisi (Contoh: Merah Glossy).',
        ]);
        
        $data = $request->all();
        $data['license_plate'] = strtoupper($request->license_plate);

        $vehicle->update($data);
        return redirect()->route('admin.vehicles.index')->with('success', 'Data kendaraan berhasil diperbarui.');
    }

    // Hapus Data Kendaraan
    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('admin.vehicles.index')->with('success', 'Data kendaraan berhasil dihapus.');
    }
}