<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException; // Tambahkan baris ini untuk menangani error database

class SparepartController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        // Mencari berdasarkan nama suku cadang
        $spareparts = Sparepart::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%");
        })->latest()->get();

        return view('admin.spareparts.index', compact('spareparts'));
    }

    public function create()
    {
        return view('admin.spareparts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0'
        ]);
        
        Sparepart::create($request->all());
        
        return redirect()->route('admin.spareparts.index')->with('success', 'Data sparepart berhasil ditambahkan.');
    }

    public function edit(Sparepart $sparepart)
    {
        return view('admin.spareparts.edit', compact('sparepart'));
    }

    public function update(Request $request, Sparepart $sparepart)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0'
        ]);
        
        $sparepart->update($request->all());
        
        return redirect()->route('admin.spareparts.index')->with('success', 'Data sparepart berhasil diperbarui.');
    }

    public function destroy(Sparepart $sparepart)
    {
        try {
            // 1. JANGAN HAPUS RIWAYAT NOTA! 
            // Cukup putuskan relasinya (SET NULL) agar fitur Snapshot Data Blade bisa bekerja.
            \App\Models\PurchaseDetail::where('sparepart_id', $sparepart->id)->update(['sparepart_id' => null]);
            \App\Models\ServiceDetail::where('sparepart_id', $sparepart->id)->update(['sparepart_id' => null]);

            // 2. Setelah relasi di nota lama diputus, Master Suku Cadang bisa dihapus dengan aman
            $sparepart->delete();

            return redirect()->route('admin.spareparts.index')->with('success', 'Suku cadang dihapus. Riwayat nota lama tetap utuh untuk pelanggan!');
            
        } catch (\Exception $e) {
            return redirect()->route('admin.spareparts.index')->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}