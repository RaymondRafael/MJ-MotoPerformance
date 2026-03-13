@extends('admin.layouts.app')
@section('title', 'Dasbor Ringkasan')
@section('header', 'Dashboard Admin')

@section('content')

@php
    // Bantuan array untuk nama bulan bahasa Indonesia
    $bulanIndo = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
@endphp

<div class="mb-6 bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
    <div>
        <h2 class="font-bold text-gray-800"><i class="fas fa-filter text-gray-400 mr-2"></i>Filter Laporan Pendapatan</h2>
        <p class="text-xs text-gray-500 mt-1">Pilih periode untuk melihat total pendapatan kotor bengkel.</p>
    </div>
    <form action="{{ route('admin.dashboard') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
        <select name="month" class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg text-sm font-bold focus:ring-red-500 focus:border-red-500 cursor-pointer">
            @foreach($bulanIndo as $angka => $nama)
                <option value="{{ $angka }}" {{ $selectedMonth == $angka ? 'selected' : '' }}>
                    {{ $nama }}
                </option>
            @endforeach
        </select>
        <select name="year" class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg text-sm font-bold focus:ring-red-500 focus:border-red-500 cursor-pointer">
            @for($i = \Carbon\Carbon::now()->year; $i >= 2024; $i--)
                <option value="{{ $i }}" {{ $selectedYear == $i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
        </select>
        <button type="submit" class="w-full sm:w-auto bg-gray-900 hover:bg-gray-800 text-white px-6 py-2 rounded-lg text-sm font-bold transition shadow-md">
            Terapkan
        </button>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 relative overflow-hidden">
        <div class="absolute right-0 top-0 w-2 h-full bg-green-500"></div>
        <div class="w-14 h-14 rounded-full bg-green-50 text-green-600 flex items-center justify-center text-2xl shadow-inner">
            <i class="fas fa-wallet"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Pendapatan ({{ $bulanIndo[(int)$selectedMonth] }} {{ $selectedYear }})</p>
            <h3 class="text-2xl font-black text-gray-800">Rp {{ number_format($pendapatan, 0, ',', '.') }}</h3>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 relative overflow-hidden">
        <div class="absolute right-0 top-0 w-2 h-full bg-red-500"></div>
        <div class="w-14 h-14 rounded-full bg-red-50 text-red-600 flex items-center justify-center text-2xl shadow-inner">
            <i class="fas fa-motorcycle"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Antrean ({{ $bulanIndo[(int)$selectedMonth] }})</p>
            <h3 class="text-2xl font-black text-gray-800">{{ $antreanAktif }} <span class="text-sm font-bold text-gray-500">Motor</span></h3>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 relative overflow-hidden">
        <div class="absolute right-0 top-0 w-2 h-full bg-blue-500"></div>
        <div class="w-14 h-14 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-2xl shadow-inner">
            <i class="fas fa-check-double"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Selesai ({{ $bulanIndo[(int)$selectedMonth] }})</p>
            <h3 class="text-2xl font-black text-gray-800">{{ $selesaiPeriode }} <span class="text-sm font-bold text-gray-500">Motor</span></h3>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 relative overflow-hidden">
        <div class="absolute right-0 top-0 w-2 h-full bg-purple-500"></div>
        <div class="w-14 h-14 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-2xl shadow-inner">
            <i class="fas fa-users"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Pendaftar ({{ $bulanIndo[(int)$selectedMonth] }})</p>
            <h3 class="text-2xl font-black text-gray-800">{{ $pelangganBaru }} <span class="text-sm font-bold text-gray-500">Orang</span></h3>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b flex justify-between items-center bg-gray-50">
        <h2 class="font-bold text-gray-700">5 Antrean Teratas Saat Ini</h2>
        <a href="{{ route('admin.services.index') }}" class="text-sm font-bold text-red-600 hover:text-red-800 transition">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white border-b border-gray-100 text-xs uppercase text-gray-500">
                    <th class="p-4 font-bold">Waktu Masuk</th>
                    <th class="p-4 font-bold">Plat Nomor</th>
                    <th class="p-4 font-bold">Pelanggan</th>
                    <th class="p-4 font-bold">Status</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                @forelse($antreanTerbaru as $antrean)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4 text-gray-500">{{ $antrean->created_at->diffForHumans() }}</td>
                    <td class="p-4 font-black text-gray-900 tracking-wider">{{ $antrean->vehicle->license_plate }}</td>
                    <td class="p-4 font-bold">{{ $antrean->vehicle->customer->name }}</td>
                    <td class="p-4">
                        @if($antrean->status == 'pending')
                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-bold uppercase"><i class="fas fa-clock mr-1"></i> Antrean</span>
                        @elseif($antrean->status == 'processing')
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-bold uppercase"><i class="fas fa-tools mr-1"></i> Dikerjakan</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-8 text-center text-gray-400">Tidak ada kendaraan dalam antrean saat ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection