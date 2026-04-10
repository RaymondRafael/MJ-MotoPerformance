@extends('admin.layouts.app')

@section('title', 'Transaksi Servis')
@section('header', 'Manajemen Servis')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    
    <div class="p-6 border-b flex flex-col md:flex-row justify-between items-center bg-gray-50 gap-4">
        <h2 class="font-bold text-gray-700">Antrean Kendaraan Hari Ini</h2>
        
        <div class="flex items-center gap-3 w-full md:w-auto">
            <form action="{{ route('admin.services.index') }}" method="GET" class="flex w-full md:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Plat/Nama/Keluhan..." 
                    class="px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-red-500 focus:border-red-500 text-sm w-full md:w-64">
                <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-r-lg border border-l-0 border-gray-300 transition">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            <a href="{{ route('admin.services.create') }}" class="bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-bold transition whitespace-nowrap">
                <i class="fas fa-plus mr-1"></i> Servis Baru
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="m-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded">
        {{ session('success') }}
    </div>
    @endif
    
    @if(session('error'))
    <div class="m-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded">
        {{ session('error') }}
    </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white border-b border-gray-100 text-xs uppercase text-gray-500">
                    <th class="p-4 font-bold">Plat Nomor</th>
                    <th class="p-4 font-bold">Pelanggan</th>
                    <th class="p-4 font-bold">Keluhan</th>
                    <th class="p-4 font-bold">Status</th>
                    <th class="p-4 font-bold text-center">Aksi (WhatsApp)</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                @forelse($services as $service)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4 font-bold text-gray-900">
                        {{ $service->vehicle->license_plate }}<br>
                        <span class="text-xs text-gray-500 font-normal">{{ $service->vehicle->brand }} {{ $service->vehicle->model }}</span>
                    </td>
                    <td class="p-4">
                        {{ $service->vehicle->customer->name }}<br>
                        <span class="text-xs text-gray-500"><i class="fab fa-whatsapp text-green-500"></i> {{ $service->vehicle->customer->phone_number }}</span>
                    </td>
                    <td class="p-4 max-w-xs truncate" title="{{ $service->complaint }}">
                        {{ $service->complaint }}
                    </td>
                    <td class="p-4">
                        <form action="{{ route('admin.services.updateStatus', $service->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <select name="status" onchange="this.form.submit()" class="text-xs font-bold rounded-lg border-gray-200 px-3 py-2 bg-white shadow-sm focus:ring-red-500 focus:border-red-500 cursor-pointer
                                {{ $service->status == 'pending' ? 'text-yellow-600' : ($service->status == 'processing' ? 'text-blue-600' : ($service->status == 'finished' ? 'text-green-600' : 'text-purple-600')) }}">
                                <option value="pending" {{ $service->status == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                <option value="processing" {{ $service->status == 'processing' ? 'selected' : '' }}>Dikerjakan</option>
                                <option value="finished" {{ $service->status == 'finished' ? 'selected' : '' }}>Selesai</option>
                                <option value="lunas" {{ $service->status == 'lunas' ? 'selected' : '' }}>Lunas</option>
                            </select>
                        </form>
                    </td>
                    <td class="p-4 text-center space-x-2 flex justify-center">
                        <a href="{{ route('admin.services.show', $service->id) }}" class="text-blue-500 hover:text-blue-700 p-2 bg-blue-50 rounded-lg" title="Lihat Detail/Nota"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.services.edit', $service->id) }}" class="text-gray-500 hover:text-gray-700 p-2 bg-gray-100 rounded-lg" title="Edit Antrean"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus riwayat servis ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 p-2 bg-red-50 rounded-lg" title="Hapus"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-gray-400">Tidak ada data servis yang ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection