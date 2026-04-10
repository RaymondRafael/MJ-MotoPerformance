@extends('admin.layouts.app')
@section('title', 'Dasbor Ringkasan')
@section('header', 'Dashboard Admin')

@section('content')

@php
    $bulanIndo = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
@endphp

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up { animation: fadeInUp 0.2s ease-out forwards; }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #e5e7eb; border-radius: 20px; }
</style>

<div class="mb-6 bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 relative z-20">
    <div>
        <h2 class="font-bold text-gray-800"><i class="fas fa-filter text-gray-400 mr-2"></i>Filter Laporan Pendapatan</h2>
        <p class="text-xs text-gray-500 mt-1">Pilih periode untuk melihat total pendapatan kotor bengkel.</p>
    </div>
    
    <form action="{{ route('admin.dashboard') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
        
        <div class="relative w-full sm:w-auto" id="dropdown-container-month">
            <input type="hidden" name="month" id="filterMonth" value="{{ $selectedMonth }}">
            
            <div id="dropdown-trigger-month" onclick="toggleDropdown('month')" class="flex items-center w-full sm:w-auto bg-gray-50 rounded-xl px-4 py-2 border border-transparent hover:border-gray-200 transition-all cursor-pointer select-none min-w-[160px]">
                <i class="fas fa-calendar-alt text-gray-500 mr-3 text-lg"></i>
                <div class="flex flex-col flex-grow pr-4">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider -mb-1">Pilih Bulan</span>
                    <span id="dropdown-label-month" class="text-sm font-bold text-gray-800 truncate max-w-[100px]">{{ $bulanIndo[(int)$selectedMonth] }}</span>
                </div>
                <i id="dropdown-icon-month" class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300"></i>
            </div>

            <div id="dropdown-menu-month" class="hidden absolute top-full left-0 mt-2 w-full min-w-[180px] bg-white border border-gray-100 rounded-xl shadow-xl overflow-hidden py-2 animate-fade-in-up">
                <div class="max-h-60 overflow-y-auto custom-scrollbar">
                    @foreach($bulanIndo as $angka => $nama)
                        <div class="dropdown-item-month px-4 py-2.5 text-sm font-semibold cursor-pointer transition-colors flex items-center justify-between {{ $selectedMonth == $angka ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" onclick="selectOption('month', '{{ $angka }}', '{{ $nama }}', this)">
                            <span>{{ $nama }}</span>
                            <i class="fas fa-check text-xs {{ $selectedMonth == $angka ? '' : 'hidden' }} text-red-500"></i>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="relative w-full sm:w-auto" id="dropdown-container-year">
            <input type="hidden" name="year" id="filterYear" value="{{ $selectedYear }}">
            
            <div id="dropdown-trigger-year" onclick="toggleDropdown('year')" class="flex items-center w-full sm:w-auto bg-gray-50 rounded-xl px-4 py-2 border border-transparent hover:border-gray-200 transition-all cursor-pointer select-none min-w-[140px]">
                <i class="fas fa-calendar-check text-gray-500 mr-3 text-lg"></i>
                <div class="flex flex-col flex-grow pr-4">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider -mb-1">Pilih Tahun</span>
                    <span id="dropdown-label-year" class="text-sm font-bold text-gray-800 truncate max-w-[80px]">{{ $selectedYear }}</span>
                </div>
                <i id="dropdown-icon-year" class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300"></i>
            </div>

            <div id="dropdown-menu-year" class="hidden absolute top-full left-0 mt-2 w-full min-w-[140px] bg-white border border-gray-100 rounded-xl shadow-xl overflow-hidden py-2 animate-fade-in-up">
                <div class="max-h-60 overflow-y-auto custom-scrollbar">
                    @for($i = \Carbon\Carbon::now()->year; $i >= 2024; $i--)
                        <div class="dropdown-item-year px-4 py-2.5 text-sm font-semibold cursor-pointer transition-colors flex items-center justify-between {{ $selectedYear == $i ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" onclick="selectOption('year', '{{ $i }}', '{{ $i }}', this)">
                            <span>{{ $i }}</span>
                            <i class="fas fa-check text-xs {{ $selectedYear == $i ? '' : 'hidden' }} text-red-500"></i>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <button type="submit" class="w-full sm:w-auto bg-gray-900 hover:bg-gray-800 text-white px-6 h-[52px] rounded-xl text-sm font-bold transition shadow-md flex items-center justify-center">
            <i class="fas fa-search mr-2"></i> Terapkan
        </button>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 relative z-10">
    
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

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative z-0">
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

<script>
    function toggleDropdown(tipe) {
        const menu = document.getElementById(`dropdown-menu-${tipe}`);
        const trigger = document.getElementById(`dropdown-trigger-${tipe}`);
        const icon = document.getElementById(`dropdown-icon-${tipe}`);

        // Tutup dropdown lain
        if(tipe === 'month') closeDropdown('year');
        if(tipe === 'year') closeDropdown('month');

        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            icon.classList.add('rotate-180', 'text-red-500');
            trigger.classList.add('border-red-300', 'ring-2', 'ring-red-100', 'bg-white');
            trigger.classList.remove('border-transparent');
        } else {
            closeDropdown(tipe);
        }
    }

    function closeDropdown(tipe) {
        const menu = document.getElementById(`dropdown-menu-${tipe}`);
        const trigger = document.getElementById(`dropdown-trigger-${tipe}`);
        const icon = document.getElementById(`dropdown-icon-${tipe}`);

        if (menu && !menu.classList.contains('hidden')) {
            menu.classList.add('hidden');
            icon.classList.remove('rotate-180', 'text-red-500');
            trigger.classList.remove('border-red-300', 'ring-2', 'ring-red-100', 'bg-white');
            trigger.classList.add('border-transparent');
        }
    }

    function selectOption(tipe, value, label, element) {
        // 1. Update Input Tersembunyi untuk dikirim ke Server
        document.getElementById(`filter${tipe.charAt(0).toUpperCase() + tipe.slice(1)}`).value = value;
        
        // 2. Update Label UI
        document.getElementById(`dropdown-label-${tipe}`).innerText = label;

        // 3. Reset warna semua opsi
        const allItems = document.querySelectorAll(`.dropdown-item-${tipe}`);
        allItems.forEach(item => {
            item.classList.remove('bg-red-50', 'text-red-600');
            item.classList.add('text-gray-600', 'hover:bg-gray-50', 'hover:text-gray-900');
            item.querySelector('.fa-check').classList.add('hidden');
        });

        // 4. Set warna opsi aktif
        element.classList.add('bg-red-50', 'text-red-600');
        element.classList.remove('text-gray-600', 'hover:bg-gray-50', 'hover:text-gray-900');
        element.querySelector('.fa-check').classList.remove('hidden');

        closeDropdown(tipe);
    }

    // Event Listener untuk menutup klik di luar area
    document.addEventListener('click', function(event) {
        if (!event.target.closest('#dropdown-container-month')) closeDropdown('month');
        if (!event.target.closest('#dropdown-container-year')) closeDropdown('year');
    });
</script>
@endsection