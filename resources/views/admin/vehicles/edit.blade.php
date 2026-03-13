@extends('admin.layouts.app')
@section('title', 'Edit Kendaraan')
@section('header', 'Manajemen Kendaraan')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 max-w-3xl mx-auto">
    <div class="mb-6 border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800"><i class="fas fa-motorcycle text-blue-500 mr-2"></i>Edit Data Kendaraan</h2>
    </div>

    <form action="{{ route('admin.vehicles.update', $vehicle->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT') <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Pemilik Kendaraan <span class="text-red-500">*</span></label>
            <select name="customer_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 transition bg-white cursor-pointer">
                <option value="">-- Pilih Nama Pelanggan --</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" {{ $vehicle->customer_id == $customer->id ? 'selected' : '' }}>
                        {{ $customer->name }} ({{ $customer->phone_number }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Plat Nomor Kendaraan <span class="text-red-500">*</span></label>
            <input type="text" name="license_plate" value="{{ $vehicle->license_plate }}" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 transition uppercase">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Merek</label>
                <input type="text" name="brand" value="{{ $vehicle->brand }}" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 transition">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Model</label>
                <input type="text" name="model" value="{{ $vehicle->model }}" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 transition">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Warna</label>
                <input type="text" name="color" value="{{ $vehicle->color }}" placeholder="Contoh: Hitam" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 transition">
            </div>
        </div>

        <div class="flex justify-end gap-4 pt-4 border-t mt-8">
            <a href="{{ route('admin.vehicles.index') }}" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 font-bold hover:bg-gray-50 transition">Batal</a>
            <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition shadow-lg shadow-blue-500/30">Update Data</button>
        </div>
    </form>
</div>
@endsection