@extends('admin.layouts.app')
@section('title', 'Data Kendaraan')
@section('header', 'Manajemen Kendaraan')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    
    <div class="p-6 border-b flex flex-col md:flex-row justify-between items-center bg-gray-50 gap-4">
        <h2 class="font-bold text-gray-700">Daftar Kendaraan Pelanggan</h2>
        
        <div class="flex items-center gap-3 w-full md:w-auto">
            <form action="{{ route('admin.vehicles.index') }}" method="GET" class="flex w-full md:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Plat Nomor/Pemilik..." 
                    class="px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-red-500 focus:border-red-500 text-sm w-full md:w-64">
                <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-r-lg border border-l-0 border-gray-300 transition">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            <a href="{{ route('admin.vehicles.create') }}" class="bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-bold transition whitespace-nowrap">
                <i class="fas fa-plus mr-1"></i> Tambah
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="m-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded">
        {{ session('success') }}
    </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white border-b border-gray-100 text-xs uppercase text-gray-500">
                    <th class="p-4 font-bold">Plat Nomor</th>
                    <th class="p-4 font-bold">Pemilik (Pelanggan)</th>
                    <th class="p-4 font-bold">Merk & Model</th>
                    <th class="p-4 font-bold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                @forelse($vehicles as $vehicle)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4 font-black text-gray-900 tracking-wider">{{ $vehicle->license_plate }}</td>
                    <td class="p-4 font-bold">{{ $vehicle->customer->name ?? '-' }}</td>
                    <td class="p-4">{{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->color }})</td>
                    <td class="p-4 text-center flex justify-center space-x-2">
                        <a href="{{ route('admin.vehicles.edit', $vehicle->id) }}" class="text-blue-500 hover:text-blue-700 p-2 bg-blue-50 rounded-lg"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.vehicles.destroy', $vehicle->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus kendaraan ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 p-2 bg-red-50 rounded-lg"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-8 text-center text-gray-400">Tidak ada data kendaraan yang ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection