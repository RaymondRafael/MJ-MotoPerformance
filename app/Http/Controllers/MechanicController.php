<?php

namespace App\Http\Controllers;

use App\Models\Mechanic;
use Illuminate\Http\Request;

class MechanicController extends Controller
{
    // Menampilkan Daftar Mekanik dengan Fitur Pencarian
    public function index(Request $request)
    {
        $search = $request->search;

        // Mencari berdasarkan nama mekanik atau nomor HP, lalu gunakan pagination
        $mechanics = Mechanic::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%");
        })->latest()->paginate(10);

        return view('admin.mechanics.index', compact('mechanics'));
    }

    // Menampilkan Form Tambah Mekanik Baru
    public function create()
    {
        return view('admin.mechanics.create');
    }

    // Menyimpan Data Mekanik Baru 
    public function store(Request $request)
    {
        // 1. Validasi input dengan aturan nomor WhatsApp
        $request->validate([
            'name'         => 'required|string|max:255',
            'phone_number' => [
                'bail',
                'required',
                'string',
                'regex:/^(08|628|\+628)[0-9]*$/',
                'min:11',
                'max:13',
                'unique:mechanics,phone_number'
            ]
        ], [
            // Kamus Error
            'name.required' => 'Nama mekanik wajib diisi.',
            'phone_number.required' => 'Nomor WhatsApp wajib diisi.',
            'phone_number.regex' => 'Format nomor tidak valid. Gunakan HANYA ANGKA (tanpa spasi/simbol - . *) dan awali dengan 08 atau 628.',
            'phone_number.min' => 'Nomor terlalu pendek. Minimal 11 angka.',
            'phone_number.max' => 'Nomor terlalu panjang. Maksimal 13 angka.',
            'phone_number.unique' => 'Nomor WhatsApp ini sudah terdaftar untuk mekanik lain.',
        ]);

        Mechanic::create($request->all());
        
        return redirect()->route('admin.mechanics.index')->with('success', 'Data mekanik berhasil ditambahkan.');
    }

    // Menampilkan Form Edit Mekanik
    public function edit(Mechanic $mechanic) 
    {
        return view('admin.mechanics.edit', compact('mechanic'));
    }
    
    // Memperbarui Data Mekanik dengan Validasi
    public function update(Request $request, Mechanic $mechanic) 
    {
        // 1. Validasi input Update 
        $request->validate([
            'name'         => 'required|string|max:255',
            'phone_number' => [
                'bail',
                'required',
                'string',
                'regex:/^(08|628|\+628)[0-9]*$/',
                'min:11',
                'max:13',
                'unique:mechanics,phone_number,' . $mechanic->id
            ]
        ], [
            // Kamus Error untuk Update
            'name.required' => 'Nama mekanik wajib diisi.',
            'phone_number.required' => 'Nomor WhatsApp wajib diisi.',
            'phone_number.regex' => 'Format nomor tidak valid. Gunakan HANYA ANGKA (tanpa spasi/simbol - . *) dan awali dengan 08 atau 628.',
            'phone_number.min' => 'Nomor terlalu pendek. Minimal 11 angka.',
            'phone_number.max' => 'Nomor terlalu panjang. Maksimal 13 angka.',
            'phone_number.unique' => 'Nomor WhatsApp ini sudah terdaftar untuk mekanik lain.',
        ]);

        $mechanic->update($request->all());
        
        return redirect()->route('admin.mechanics.index')->with('success', 'Data mekanik berhasil diubah.');
    }

    public function destroy(Mechanic $mechanic) 
    {
        try {
            $mechanic->delete();
            return redirect()->route('admin.mechanics.index')->with('success', 'Data mekanik dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return redirect()->back()->with('error', 'Gagal menghapus! Mekanik ini masih memiliki riwayat pengerjaan di nota servis.');
            }
        return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}