@extends('admin.layouts.app')
@section('title', 'Tambah Kendaraan')
@section('header', 'Manajemen Kendaraan')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 max-w-3xl mx-auto">
    <div class="mb-6 border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800"><i class="fas fa-motorcycle text-red-500 mr-2"></i>Form Tambah Kendaraan</h2>
    </div>

    <form action="{{ route('admin.vehicles.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Pemilik Kendaraan <span class="text-red-500">*</span></label>
            <select name="customer_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-red-500 focus:border-red-500 transition bg-white cursor-pointer">
                <option value="">-- Pilih Nama Pelanggan --</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone_number }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Plat Nomor Kendaraan <span class="text-red-500">*</span></label>
            <input type="text" name="license_plate" required placeholder="Contoh: D 1234 ABC"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-red-500 focus:border-red-500 transition uppercase">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Merek</label>
                <input type="text" name="brand" required placeholder="Contoh: Honda"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-red-500 focus:border-red-500 transition">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Model</label>
                <input type="text" name="model" required placeholder="Contoh: Vario 150"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-red-500 focus:border-red-500 transition">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Warna</label>
                <input type="text" name="color" placeholder="Contoh: Hitam"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-red-500 focus:border-red-500 transition">
            </div>
        </div>

        <div class="flex justify-end gap-4 pt-4 border-t mt-8">
            <a href="{{ route('admin.vehicles.index') }}" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 font-bold hover:bg-gray-50 transition">Batal</a>
            <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition shadow-lg shadow-red-500/30">Simpan Data</button>
        </div>
    </form>
</div>
@endsection