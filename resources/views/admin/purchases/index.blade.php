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
            <form action="{{ route('admin.purchases.index') }}" method="GET" id="filterForm" class="flex flex-col sm:flex-row w-full lg:w-auto gap-3">
                
                <div id="dropdown-container-month" class="relative w-full sm:w-44">
                    <input type="hidden" name="month" id="filterMonth" value="{{ request('month') }}">
                    
                    <div id="dropdown-trigger-month" onclick="toggleDropdown('month')" class="w-full px-4 py-2.5 bg-gray-50 border {{ request('month') ? 'border-red-300 ring-2 ring-red-100 bg-white' : 'border-transparent' }} rounded-lg flex items-center justify-between cursor-pointer transition-all shadow-sm">
                        <span class="text-sm font-bold {{ request('month') ? 'text-gray-900' : 'text-gray-600' }} truncate" id="dropdown-label-month">
                            {{ request('month') && isset($months[request('month')]) ? $months[request('month')] : '-- Semua Bulan --' }}
                        </span>
                        <i id="dropdown-icon-month" class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-300"></i>
                    </div>

                    <div id="dropdown-menu-month" class="hidden absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-xl overflow-hidden max-h-60 overflow-y-auto custom-scrollbar">
                        <div class="p-1.5 space-y-1">
                            <div class="dropdown-item-month px-3 py-2.5 rounded-lg transition-colors font-bold text-xs flex items-center justify-between cursor-pointer {{ request('month') == '' ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" onclick="selectOption('month', '', '-- Semua Bulan --', this)">
                                -- Semua Bulan --
                                <i class="fas fa-check text-red-500 {{ request('month') == '' ? '' : 'hidden' }}"></i>
                            </div>
                            @foreach($months as $num => $name)
                            <div class="dropdown-item-month px-3 py-2.5 rounded-lg transition-colors font-bold text-xs flex items-center justify-between cursor-pointer {{ request('month') == $num ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" onclick="selectOption('month', '{{ $num }}', '{{ $name }}', this)">
                                {{ $name }}
                                <i class="fas fa-check text-red-500 {{ request('month') == $num ? '' : 'hidden' }}"></i>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div id="dropdown-container-year" class="relative w-full sm:w-40">
                    <input type="hidden" name="year" id="filterYear" value="{{ request('year') }}">
                    
                    <div id="dropdown-trigger-year" onclick="toggleDropdown('year')" class="w-full px-4 py-2.5 bg-gray-50 border {{ request('year') ? 'border-red-300 ring-2 ring-red-100 bg-white' : 'border-transparent' }} rounded-lg flex items-center justify-between cursor-pointer transition-all shadow-sm">
                        <span class="text-sm font-bold {{ request('year') ? 'text-gray-900' : 'text-gray-600' }} truncate" id="dropdown-label-year">
                            {{ request('year') ?: '-- Semua Tahun --' }}
                        </span>
                        <i id="dropdown-icon-year" class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-300"></i>
                    </div>

                    <div id="dropdown-menu-year" class="hidden absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-xl overflow-hidden max-h-60 overflow-y-auto custom-scrollbar">
                        <div class="p-1.5 space-y-1">
                            <div class="dropdown-item-year px-3 py-2.5 rounded-lg transition-colors font-bold text-xs flex items-center justify-between cursor-pointer {{ request('year') == '' ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" onclick="selectOption('year', '', '-- Semua Tahun --', this)">
                                -- Semua Tahun --
                                <i class="fas fa-check text-red-500 {{ request('year') == '' ? '' : 'hidden' }}"></i>
                            </div>
                            @foreach($years as $yr)
                            <div class="dropdown-item-year px-3 py-2.5 rounded-lg transition-colors font-bold text-xs flex items-center justify-between cursor-pointer {{ request('year') == $yr ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" onclick="selectOption('year', '{{ $yr }}', '{{ $yr }}', this)">
                                {{ $yr }}
                                <i class="fas fa-check text-red-500 {{ request('year') == $yr ? '' : 'hidden' }}"></i>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex w-full sm:w-auto shadow-sm rounded-lg">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama supplier..." 
                        class="px-4 py-2.5 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-red-100 focus:border-red-400 outline-none text-sm w-full sm:w-56 font-medium transition-all">
                    <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-5 py-2.5 rounded-r-lg border border-l-0 border-gray-300 transition-colors">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            
            @if(request('search') || request('month') || request('year'))
                <a href="{{ route('admin.purchases.index') }}" class="bg-white border border-gray-300 text-gray-500 hover:text-red-500 hover:bg-red-50 px-4 py-2.5 rounded-lg transition-colors shadow-sm" title="Reset Filter">
                    <i class="fas fa-sync-alt"></i>
                </a>
            @endif

            <a href="{{ route('admin.purchases.create') }}" class="bg-gray-900 hover:bg-gray-800 text-white px-4 py-2.5 rounded-lg text-sm font-bold transition flex items-center shadow-sm whitespace-nowrap">
                <i class="fas fa-plus mr-2"></i> Terima Barang
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white border-b border-gray-100 text-xs uppercase tracking-wider text-gray-400">
                    <th class="p-5 font-bold text-center w-16">No.</th> 
                    <th class="p-5 font-bold">Detail Nota</th>
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
                        <p class="font-bold text-gray-900 mb-1">#PRC-{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}</p>
                        <p class="text-xs font-medium text-gray-500 flex items-center gap-1.5">
                            <i class="far fa-calendar-alt text-gray-400"></i> {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}
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
            
            // Cek apakah filter tipe ini sedang aktif (punya nilai)
            const isActive = document.getElementById(`filter${tipe.charAt(0).toUpperCase() + tipe.slice(1)}`).value !== "";
            
            if(!isActive) {
                trigger.classList.remove('border-red-300', 'ring-2', 'ring-red-100', 'bg-white');
                trigger.classList.add('border-transparent');
            }
        }
    }

    function selectOption(tipe, value, label, element) {
        // 1. Update Input Tersembunyi
        document.getElementById(`filter${tipe.charAt(0).toUpperCase() + tipe.slice(1)}`).value = value;
        
        // 2. Update Label UI
        const labelEl = document.getElementById(`dropdown-label-${tipe}`);
        labelEl.innerText = label;
        labelEl.classList.remove('text-gray-600');
        labelEl.classList.add('text-gray-900');

        // 3. Reset warna semua opsi
        const allItems = document.querySelectorAll(`.dropdown-item-${tipe}`);
        allItems.forEach(item => {
            item.classList.remove('bg-red-50', 'text-red-600');
            item.classList.add('text-gray-600', 'hover:bg-gray-50', 'hover:text-gray-900');
            const checkIcon = item.querySelector('.fa-check');
            if(checkIcon) checkIcon.classList.add('hidden');
        });

        // 4. Set warna opsi aktif
        element.classList.add('bg-red-50', 'text-red-600');
        element.classList.remove('text-gray-600', 'hover:bg-gray-50', 'hover:text-gray-900');
        const activeCheck = element.querySelector('.fa-check');
        if(activeCheck) activeCheck.classList.remove('hidden');

        closeDropdown(tipe);

        // 5. Submit Form Otomatis
        document.getElementById('filterForm').submit();
    }

    // Event Listener untuk menutup klik di luar area
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