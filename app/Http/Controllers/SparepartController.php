<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use Illuminate\Http\Request;

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
        $sparepart->delete();
        return redirect()->route('admin.spareparts.index')->with('success', 'Data sparepart berhasil dihapus.');
    }
}