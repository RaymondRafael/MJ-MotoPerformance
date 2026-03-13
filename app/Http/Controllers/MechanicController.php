<?php

namespace App\Http\Controllers;

use App\Models\Mechanic;
use Illuminate\Http\Request;

class MechanicController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        // Mencari berdasarkan nama mekanik
        $mechanics = Mechanic::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%");
        })->latest()->get();

        return view('admin.mechanics.index', compact('mechanics'));
    }

    public function create()
    {
        return view('admin.mechanics.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required', 'phone_number' => 'nullable']);
        Mechanic::create($request->all());
        return redirect()->route('admin.mechanics.index')->with('success', 'Data mekanik berhasil ditambahkan.');
    }

    public function edit(Mechanic $mechanic) {
        return view('admin.mechanics.edit', compact('mechanic'));
    }
    
    public function update(Request $request, Mechanic $mechanic) {
        $request->validate(['name' => 'required', 'phone_number' => 'nullable']);
        $mechanic->update($request->all());
        return redirect()->route('admin.mechanics.index')->with('success', 'Data mekanik berhasil diubah.');
    }

    public function destroy(Mechanic $mechanic) {
        $mechanic->delete();
        return redirect()->route('admin.mechanics.index')->with('success', 'Data mekanik dihapus.');
    }
}