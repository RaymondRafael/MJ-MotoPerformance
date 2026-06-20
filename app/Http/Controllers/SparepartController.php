<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use Illuminate\Http\Request;

class SparepartController extends Controller
{
    // 1. Menampilkan Daftar Suku Cadang dengan Pencarian & Filter Kategori
    public function index(Request $request)
    {
        $search = $request->search;
        $selectedCategory = $request->category; 

        $spareparts = Sparepart::when($search, function ($query, $search) {
                // Membungkus pencarian nama ATAU merek dengan group where
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%"); 
                });
            })
            ->when($selectedCategory, function ($query, $selectedCategory) {
                return $query->where('category', $selectedCategory); 
            })
            ->latest()
            ->paginate(10);

        $categories = ['Oli', 'Shockbreaker', 'Roller', 'Vanbelt', 'Busi', 'Sistem Rem', 'Ban', 'Air Radiator', 'Handgrip', 'Lainnya'];

        return view('admin.spareparts.index', compact('spareparts', 'categories'));
    }

    public function create()
    {
        $categories = ['Oli', 'Shockbreaker', 'Roller', 'Vanbelt', 'Busi', 'Sistem Rem', 'Ban', 'Air Radiator', 'Handgrip' ,'Lainnya'];
        return view('admin.spareparts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'brand' => 'nullable',
            'category' => 'required', 
            'price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0'
        ]);
        
        Sparepart::create($request->all());
        return redirect()->route('admin.spareparts.index')->with('success', 'Data suku cadang berhasil ditambahkan.');
    }

    public function edit(Sparepart $sparepart)
    {
        $categories = ['Oli', 'Shockbreaker', 'Roller', 'Vanbelt', 'Busi', 'Sistem Rem', 'Ban', 'Air Radiator', 'Handgrip', 'Lainnya'];
        return view('admin.spareparts.edit', compact('sparepart', 'categories'));
    }

    public function update(Request $request, Sparepart $sparepart)
    {
        $request->validate([
            'name' => 'required',
            'brand' => 'nullable',
            'category' => 'required',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0'
        ]);
        
        $sparepart->update($request->all());
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