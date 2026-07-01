<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\Category;
use Illuminate\Http\Request;

class SparepartController extends Controller
{
    // 1. Menampilkan Daftar Suku Cadang dengan Pencarian & Filter Kategori
    public function index(Request $request)
    {
        $query = Sparepart::query();

        // 1. REVISI: Filter berdasarkan kategori menggunakan relasi tabel
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        // 2. REVISI: Pencarian berdasarkan nama, kode, merek, atau nama kategori lewat tabel relasi
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")
                ->orWhere('brand', 'like', "%{$search}%")
                ->orWhereHas('category', function($qc) use ($search) {
                    $qc->where('name', 'like', "%{$search}%");
                });
            });
        }

        // 3. REVISI: Ambil semua daftar kategori dari tabel categories untuk filter dropdown agar tidak error
        $categories = Category::distinct()->pluck('name')->filter()->toArray();

        // Eager load relasi category agar query lebih efisien dan cepat
        $spareparts = $query->with('category')->latest()->paginate(10);

        return view('admin.spareparts.index', compact('spareparts', 'categories'));
    }

    public function edit(Sparepart $sparepart)
    {
        // Mengambil daftar kategori dari database untuk mempermudah opsi edit pilihan
        $categories = Category::distinct()->pluck('name')->filter()->toArray();
        return view('admin.spareparts.edit', compact('sparepart', 'categories'));
    }

    public function update(Request $request, Sparepart $sparepart)
    {
        // 1. Bersihkan format titik pada inputan harga
        $request->merge([
            'price' => str_replace('.', '', $request->price)
        ]);

        // 2. Validasi aturan
        $request->validate([
            'code' => 'required|string|max:255|unique:spareparts,code,' . $sparepart->id,
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:1',
        ], [
            // Kamus Error
            'code.required' => 'Kode barang wajib diisi.',
            'code.unique' => 'Kode barang ini sudah terdaftar di sistem.',
            'name.required' => 'Nama barang wajib diisi.',
            'brand.required' => 'Merek barang wajib diisi.',
            'category.required' => 'Kategori barang wajib diisi.',
            'price.required' => 'Harga jual barang wajib diisi.',
            'price.min' => 'Harga jual barang tidak boleh Rp 0.' 
        ]);
        
        // REVISI: Cari kategori berdasarkan teks input, jika tidak ada maka otomatis dibuat baru
        $category = Category::firstOrCreate([
            'name' => trim($request->category)
        ]);

        // 3. REVISI: Simpan perubahan ke dalam field category_id secara aman
        $sparepart->update([
            'code' => $request->code,
            'name' => $request->name,
            'brand' => $request->brand,
            'category_id' => $category->id,
            'price' => $request->price,
        ]);
        
        return redirect()->route('admin.spareparts.index')->with('success', 'Data suku cadang berhasil diperbarui.');
    }

    public function destroy(Sparepart $sparepart)
    {
        try {
            \App\Models\PurchaseDetail::where('sparepart_id', $sparepart->id)->update(['sparepart_id' => null]);
            \App\Models\ServiceDetail::where('sparepart_id', $sparepart->id)->update(['sparepart_id' => null]);

            $sparepart->delete();
            return redirect()->route('admin.spareparts.index')->with('success', 'Suku cadang berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.spareparts.index')->with('error', 'Terjadi kesalahan sistem.');
        }
    }
}