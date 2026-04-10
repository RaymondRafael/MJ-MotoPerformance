@extends('admin.layouts.app')
@section('title', 'Tambah Suku Cadang')
@section('header', 'Inventaris Suku Cadang')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 max-w-3xl mx-auto">
    <div class="mb-6 border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800"><i class="fas fa-box text-red-500 mr-2"></i>Form Tambah Stok Baru</h2>
    </div>

    <form action="{{ route('admin.spareparts.store') }}" method="POST" class="space-y-6">
        @csrf
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Nama Suku Cadang <span class="text-red-500">*</span></label>
            <input type="text" name="name" required placeholder="Contoh: Oli Mesin Motul 1L"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-red-500 focus:border-red-500 transition">
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Harga Jual (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="price" required min="0" placeholder="Contoh: 75000"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-red-500 focus:border-red-500 transition">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Stok Awal (Pcs) <span class="text-red-500">*</span></label>
                <input type="number" name="stock" required min="0" placeholder="Contoh: 50"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-red-500 focus:border-red-500 transition">
            </div>
        </div>

        <div class="flex justify-end gap-4 pt-4 border-t mt-8">
            <a href="{{ route('admin.spareparts.index') }}" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 font-bold hover:bg-gray-50 transition">Batal</a>
            <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition shadow-lg shadow-red-500/30">Simpan Data</button>
        </div>
    </form>
</div>
@endsection