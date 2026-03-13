@extends('admin.layouts.app')
@section('title', 'Edit Suku Cadang')
@section('header', 'Inventaris Suku Cadang')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 max-w-3xl mx-auto">
    <div class="mb-6 border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800"><i class="fas fa-edit text-blue-500 mr-2"></i>Edit Data Suku Cadang</h2>
    </div>

    <form action="{{ route('admin.spareparts.update', $sparepart->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT') <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Nama Suku Cadang <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ $sparepart->name }}" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 transition">
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Harga Jual (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="price" value="{{ $sparepart->price }}" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 transition">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Stok Tersedia <span class="text-red-500">*</span></label>
                <input type="number" name="stock" value="{{ $sparepart->stock }}" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 transition">
            </div>
        </div>

        <div class="flex justify-end gap-4 pt-4 border-t mt-8">
            <a href="{{ route('admin.spareparts.index') }}" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 font-bold hover:bg-gray-50 transition">Batal</a>
            <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition shadow-lg shadow-blue-500/30">Update Data</button>
        </div>
    </form>
</div>
@endsection