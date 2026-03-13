@extends('admin.layouts.app')
@section('title', 'Buka Servis Baru')
@section('header', 'Transaksi Servis')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 max-w-3xl mx-auto">
    <div class="mb-6 border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800"><i class="fas fa-clipboard-check text-red-500 mr-2"></i>Form Buka Antrean Servis</h2>
    </div>

    <form action="{{ route('admin.services.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Kendaraan (Plat Nomor) <span class="text-red-500">*</span></label>
                <select name="vehicle_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-red-500 focus:border-red-500 transition bg-white cursor-pointer font-bold">
                    <option value="">-- Pilih Kendaraan --</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}">{{ $vehicle->license_plate }} ({{ $vehicle->customer->name }})</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Mekanik <span class="text-red-500">*</span></label>
                <select name="mechanic_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-red-500 focus:border-red-500 transition bg-white cursor-pointer">
                    <option value="">-- Pilih Mekanik --</option>
                    @foreach($mechanics as $mechanic)
                        <option value="{{ $mechanic->id }}">{{ $mechanic->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Keluhan Pelanggan <span class="text-red-500">*</span></label>
            <textarea name="complaint" required rows="3" placeholder="Contoh: Tarikan gas berat, minta ganti oli gardan..."
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-red-500 focus:border-red-500 transition"></textarea>
        </div>

        <input type="hidden" name="status" value="pending">

        <div class="flex justify-end gap-4 pt-4 border-t mt-8">
            <a href="{{ route('admin.services.index') }}" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 font-bold hover:bg-gray-50 transition">Batal</a>
            <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition shadow-lg shadow-red-500/30">Mulai Antrean</button>
        </div>
    </form>
</div>
@endsection