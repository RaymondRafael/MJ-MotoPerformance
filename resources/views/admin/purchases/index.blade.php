@extends('admin.layouts.app')
@section('title', 'Data Pembelian')
@section('header', 'Inventaris & Pembelian')

@section('content')

@if (session('success'))
    <div class="mb-6 p-5 bg-green-50 border-l-4 border-green-500 rounded-r-xl shadow-sm flex items-start gap-4 transition-all">
        <div class="mt-0.5 text-green-500 text-xl"><i class="fas fa-check-circle"></i></div>
        <div>
            <h3 class="text-sm font-bold text-green-800 mb-1">Berhasil!</h3>
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    </div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-4">
    <div class="p-6 border-b flex flex-col md:flex-row justify-between items-center bg-gray-50 gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white border border-gray-200 text-red-600 rounded-lg flex items-center justify-center shadow-sm">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <h2 class="font-bold text-gray-800 text-lg tracking-tight">Riwayat Pembelian Barang</h2>
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
            <form action="{{ route('admin.purchases.index') }}" method="GET" id="filterForm" class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
                
                <div class="relative w-full sm:w-auto" id="dropdown-container-month">
                    <input type="hidden" name="month" id="filterMonth" value="{{ request('month') }}">
                    
                    <div id="dropdown-trigger-month" onclick="toggleDropdown('month')" class="flex items-center w-full sm:w-auto bg-gray-50 hover:bg-white rounded-xl px-4 py-2 border border-gray-200 hover:border-gray-300 transition-all cursor-pointer select-none min-w-[160px] h-[52px]">
                        <i class="fas fa-calendar-alt text-gray-500 mr-3 text-lg"></i>
                        <div class="flex flex-col flex-grow pr-4">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider -mb-1">Pilih Bulan</span>
                            <span id="dropdown-label-month" class="text-sm font-bold text-gray-800 truncate max-w-[100px]">
                                {{ request('month') && isset($months[request('month')]) ? $months[request('month')] : 'Semua Bulan' }}
                            </span>
                        </div>
                        <i id="dropdown-icon-month" class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300"></i>
                    </div>

                    <div id="dropdown-menu-month" class="hidden absolute top-full left-0 mt-2 w-full min-w-[180px] bg-white border border-gray-100 rounded-xl shadow-xl overflow-hidden py-2 z-50">
                        <div class="max-h-60 overflow-y-auto custom-scrollbar">
                            <div class="dropdown-item-month px-4 py-2.5 text-sm font-semibold cursor-pointer transition-colors flex items-center justify-between {{ request('month') == '' ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" onclick="selectOption('month', '', 'Semua Bulan', this)">
                                <span>Semua Bulan</span>
                                <i class="fas fa-check text-xs {{ request('month') == '' ? '' : 'hidden' }} text-red-500"></i>
                            </div>
                            @foreach($months as $angka => $nama)
                                <div class="dropdown-item-month px-4 py-2.5 text-sm font-semibold cursor-pointer transition-colors flex items-center justify-between {{ request('month') == $angka ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" onclick="selectOption('month', '{{ $angka }}', '{{ $nama }}', this)">
                                    <span>{{ $nama }}</span>
                                    <i class="fas fa-check text-xs {{ request('month') == $angka ? '' : 'hidden' }} text-red-500"></i>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="relative w-full sm:w-auto" id="dropdown-container-year">
                    <input type="hidden" name="year" id="filterYear" value="{{ request('year') }}">
                    
                    <div id="dropdown-trigger-year" onclick="toggleDropdown('year')" class="flex items-center w-full sm:w-auto bg-gray-50 hover:bg-white rounded-xl px-4 py-2 border border-gray-200 hover:border-gray-300 transition-all cursor-pointer select-none min-w-[140px] h-[52px]">
                        <i class="fas fa-calendar-check text-gray-500 mr-3 text-lg"></i>
                        <div class="flex flex-col flex-grow pr-4">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider -mb-1">Pilih Tahun</span>
                            <span id="dropdown-label-year" class="text-sm font-bold text-gray-800 truncate max-w-[80px]">
                                {{ request('year') ?: 'Semua Tahun' }}
                            </span>
                        </div>
                        <i id="dropdown-icon-year" class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300"></i>
                    </div>

                    <div id="dropdown-menu-year" class="hidden absolute top-full left-0 mt-2 w-full min-w-[140px] bg-white border border-gray-100 rounded-xl shadow-xl overflow-hidden py-2 z-50">
                        <div class="max-h-60 overflow-y-auto custom-scrollbar">
                            <div class="dropdown-item-year px-4 py-2.5 text-sm font-semibold cursor-pointer transition-colors flex items-center justify-between {{ request('year') == '' ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" onclick="selectOption('year', '', 'Semua Tahun', this)">
                                <span>Semua Tahun</span>
                                <i class="fas fa-check text-xs {{ request('year') == '' ? '' : 'hidden' }} text-red-500"></i>
                            </div>
                            @foreach($years as $yr)
                                <div class="dropdown-item-year px-4 py-2.5 text-sm font-semibold cursor-pointer transition-colors flex items-center justify-between {{ request('year') == $yr ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" onclick="selectOption('year', '{{ $yr }}', '{{ $yr }}', this)">
                                    <span>{{ $yr }}</span>
                                    <i class="fas fa-check text-xs {{ request('year') == $yr ? '' : 'hidden' }} text-red-500"></i>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex w-full sm:w-auto shadow-sm rounded-xl">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama supplier..." 
                        class="pl-5 pr-4 py-3 border border-gray-200 rounded-l-xl focus:ring-2 focus:ring-red-100 focus:border-red-400 outline-none text-sm w-full sm:w-56 font-bold text-gray-700 bg-white transition-all h-[52px]">
                    <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-5 py-3 rounded-r-xl border border-l-0 border-gray-200 transition-colors h-[52px]">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            
            @if(request('search') || request('month') || request('year'))
                <a href="{{ route('admin.purchases.index') }}" class="bg-white border border-gray-300 text-gray-500 hover:text-red-500 hover:bg-red-50 px-4 py-2.5 rounded-lg transition-colors shadow-sm" title="Reset Filter">
                    <i class="fas fa-sync-alt"></i>
                </a>
            @endif

            <a href="{{ route('admin.purchases.create') }}" class="bg-gray-900 hover:bg-gray-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center justify-center shadow-sm whitespace-nowrap w-full lg:w-auto h-[52px]">
                <i class="fas fa-plus mr-2"></i> Terima Barang
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white border-b border-gray-100 text-xs uppercase tracking-wider text-gray-400">
                    <th class="p-5 font-bold text-center w-16">No.</th> 
                    <th class="p-5 font-bold">Tanggal Masuk</th>
                    <th class="p-5 font-bold">Nama Supplier</th>
                    <th class="p-5 font-bold text-right">Total Tagihan</th>
                    <th class="p-5 font-bold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 divide-y divide-gray-50">
                @forelse($purchases as $purchase)
                <tr class="hover:bg-gray-50/80 transition-colors group">
                    <td class="p-5 font-bold text-gray-900 text-center">
                        {{ ($purchases->currentPage() - 1) * $purchases->perPage() + $loop->iteration }}
                    </td>
                    <td class="p-5">
                        <!-- <p class="font-bold text-gray-900 mb-1">#PRC-{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}</p> -->
                        <p class="text-m font-medium text-gray-500 flex items-center gap-1.5">
                            <i class="far fa-calendar-alt text-gray-10000"></i> {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}
                        </p>
                    </td>
                    <td class="p-5 font-bold text-gray-800">
                        {{ $purchase->supplier_name }}
                    </td>
                    <td class="p-5 text-right font-black text-red-600 text-base">
                        <span class="text-xs font-bold text-red-400 mr-1">Rp</span>{{ number_format($purchase->total_cost, 0, ',', '.') }}
                    </td>
                    <td class="p-5 text-center">
                        <a href="{{ route('admin.purchases.show', $purchase->id) }}" class="inline-flex items-center justify-center bg-white border border-gray-200 text-blue-600 hover:bg-blue-50 hover:border-blue-200 px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm">
                            <i class="fas fa-eye mr-1.5"></i> Rincian
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-10 text-center">
                        <div class="w-16 h-16 bg-gray-50 border border-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3 text-gray-300">
                            <i class="fas fa-receipt text-2xl"></i>
                        </div>
                        <p class="text-gray-500 font-bold">Tidak ada catatan pembelian ditemukan.</p>
                        @if(request('search') || request('month') || request('year'))
                            <p class="text-gray-400 text-xs mt-1">Coba gunakan filter bulan, tahun, atau kata kunci lain.</p>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($purchases->hasPages())
        <div class="p-5 border-t border-gray-100 bg-gray-50">
            {{ $purchases->withQueryString()->links() }}
        </div>
    @endif
</div>

<script>
    function toggleDropdown(tipe) {
        const menu = document.getElementById(`dropdown-menu-${tipe}`);
        const trigger = document.getElementById(`dropdown-trigger-${tipe}`);
        const icon = document.getElementById(`dropdown-icon-${tipe}`);

        // Tutup dropdown sebelahnya
        if(tipe === 'month') closeDropdown('year');
        if(tipe === 'year') closeDropdown('month');

        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            icon.classList.add('rotate-180', 'text-red-500');
            trigger.classList.add('border-red-300', 'ring-2', 'ring-red-100', 'bg-white');
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
            trigger.classList.add('border-gray-200');
        }
    }

    function selectOption(tipe, value, label, element) {
        // 1. Simpan nilai ke input hidden
        document.getElementById(`filter${tipe.charAt(0).toUpperCase() + tipe.slice(1)}`).value = value;
        
        // 2. Ubah teks label utama
        document.getElementById(`dropdown-label-${tipe}`).innerText = label;

        // 3. Bersihkan gaya aktif dari semua item
        const allItems = document.querySelectorAll(`.dropdown-item-${tipe}`);
        allItems.forEach(item => {
            item.classList.remove('bg-red-50', 'text-red-600');
            item.classList.add('text-gray-600', 'hover:bg-gray-50', 'hover:text-gray-900');
            item.querySelector('.fa-check').classList.add('hidden');
        });

        // 4. Terapkan gaya aktif pada item yang diklik
        element.classList.add('bg-red-50', 'text-red-600');
        element.classList.remove('text-gray-600', 'hover:bg-gray-50', 'hover:text-gray-900');
        element.querySelector('.fa-check').classList.remove('hidden');

        closeDropdown(tipe);

        // 5. EKSEKUSI PENCARIAN OTOMATIS
        document.getElementById('filterForm').submit();
    }

    // Klik di luar area dropdown untuk menutup
    document.addEventListener('click', function(event) {
        if (!event.target.closest('#dropdown-container-month')) closeDropdown('month');
        if (!event.target.closest('#dropdown-container-year')) closeDropdown('year');
    });
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

@endsection