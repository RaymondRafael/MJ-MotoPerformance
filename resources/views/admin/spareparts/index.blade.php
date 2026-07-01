@extends('admin.layouts.app')
@section('title', 'Data Suku Cadang')
@section('header', 'Inventaris Suku Cadang')

@section('content')

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

@if (session('success'))
    <div class="mb-6 p-5 bg-green-50 border-l-4 border-green-500 rounded-r-xl shadow-sm flex items-start gap-4 transition-all animate-fade-in-down">
        <div class="mt-0.5 text-green-500 text-xl"><i class="fas fa-check-circle"></i></div>
        <div>
            <h3 class="text-sm font-bold text-green-800 mb-1">Berhasil!</h3>
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    </div>
@endif

@if (session('error'))
    <div class="mb-6 p-5 bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-sm flex items-start gap-4 transition-all">
        <div class="mt-0.5 text-red-500 text-xl"><i class="fas fa-exclamation-circle"></i></div>
        <div>
            <h3 class="text-sm font-bold text-red-800 mb-1">Aksi Ditolak:</h3>
            <p class="text-sm text-red-700">{{ session('error') }}</p>
        </div>
    </div>
@endif

<div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden mt-4">
    
    <div class="p-6 md:p-8 border-b border-gray-100 flex flex-col xl:flex-row justify-between items-center bg-gray-50/50 gap-6">
        <div class="flex items-center gap-4 w-full xl:w-auto justify-center xl:justify-start">
            <div class="w-12 h-12 bg-white border border-gray-200 text-blue-600 rounded-2xl flex items-center justify-center shadow-sm">
                <i class="fas fa-boxes text-xl"></i>
            </div>
            <div>
                <h2 class="font-black text-gray-900 text-xl tracking-tight">Katalog Suku Cadang</h2>
                <p class="text-xs font-bold text-gray-500 mt-1">Kelola data master, stok, dan harga jual barang.</p>
            </div>
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full xl:w-auto">
            <form action="{{ route('admin.spareparts.index') }}" method="GET" id="filterForm" class="flex flex-col sm:flex-row w-full xl:w-auto gap-3">
                
                <div class="relative custom-dropdown w-full sm:w-56" data-type="category-filter">
                    <input type="hidden" name="category" id="filter_category" value="{{ request('category') }}">
                    
                    <div class="dropdown-trigger w-full pl-11 pr-10 py-3 bg-white border border-gray-200 rounded-xl flex items-center justify-between cursor-pointer hover:border-blue-300 focus:ring-4 focus:ring-blue-50 transition-all group shadow-sm h-[52px]" onclick="toggleCustomDropdown(this)">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-tags text-sm"></i>
                        </div>
                        <span class="text-sm font-bold text-gray-700 selected-label truncate">
                            {{ request('category') ?: 'Semua Kategori' }}
                        </span>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400 transition-transform duration-300 arrow-icon">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>

                    <div class="dropdown-menu hidden absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden max-h-60 overflow-y-auto custom-scrollbar">
                        <div class="p-1.5 space-y-1">
                            <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer rounded-xl transition-colors font-bold text-gray-500 text-sm" onclick="selectFilterOption(this, '', '-- Semua Kategori --')">
                                -- Semua Kategori --
                            </div>
                            @foreach($categories as $cat)
                                <div class="px-4 py-3 hover:bg-blue-50 cursor-pointer rounded-xl transition-colors font-bold text-gray-800 hover:text-blue-600 text-sm flex items-center justify-between" onclick="selectFilterOption(this, '{{ $cat }}', '{{ $cat }}')">
                                    {{ $cat }}
                                    @if(request('category') == $cat)
                                        <i class="fas fa-check-circle text-blue-500"></i>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex shadow-sm rounded-xl bg-white w-full sm:w-auto h-[52px]">
                    <div class="relative w-full sm:w-64">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-search text-sm"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, nama, atau merek..." 
                            class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-l-xl focus:ring-2 focus:ring-blue-50 focus:border-blue-400 outline-none text-sm font-bold text-gray-700 transition-all h-full">
                    </div>
                    <button type="submit" class="bg-gray-50 hover:bg-gray-100 text-gray-600 px-5 rounded-r-xl border border-l-0 border-gray-200 transition-colors font-bold text-sm h-full flex items-center justify-center">
                        Cari
                    </button>
                </div>
            </form>
            
            @if(request('search') || request('category'))
                <a href="{{ route('admin.spareparts.index') }}" class="flex items-center justify-center w-full sm:w-12 h-[52px] bg-red-50 text-red-500 border border-red-100 hover:bg-red-100 rounded-xl transition-colors shadow-sm" title="Reset Filter">
                    <i class="fas fa-sync-alt mr-2 sm:mr-0"></i> <span class="sm:hidden font-bold">Reset Pencarian</span>
                </a>
            @endif
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white border-b border-gray-100 text-[11px] font-black uppercase tracking-widest text-gray-400">
                    <th class="p-5 text-center w-16">No</th> 
                    <th class="p-5">Kode Barang</th>
                    <th class="p-5">Nama Suku Cadang</th>
                    <th class="p-5">Merek</th>
                    <th class="p-5 text-right">Harga Jual</th>
                    <th class="p-5 text-center">Stok</th>
                    <th class="p-5 text-center w-28">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 divide-y divide-gray-50">
                @forelse($spareparts as $sparepart)
                <tr class="hover:bg-blue-50/30 transition-colors group">
                    <td class="p-5 font-bold text-gray-400 text-center">
                        {{ ($spareparts->currentPage() - 1) * $spareparts->perPage() + $loop->iteration }}
                    </td>
                    
                    <td class="p-5">
                        <span class="px-3 py-1.5 bg-gray-100 border border-gray-200 rounded-lg text-xs font-mono font-bold text-gray-700 shadow-sm inline-block uppercase">
                            <i class="fas fa-barcode mr-1.5 text-gray-400"></i>{{ $sparepart->code ?? 'KOSONG' }}
                        </span>
                    </td>

                    <td class="p-5">
                        <p class="font-black text-gray-900 text-base mb-1">{{ $sparepart->name }}</p>
                        <span class="text-[10px] uppercase tracking-widest bg-blue-50 text-blue-600 px-2 py-0.5 rounded border border-blue-100 font-bold">
                            {{ $sparepart->category->name ?? 'Umum' }}
                        </span>
                    </td>

                    <td class="p-5 font-bold text-gray-600">
                        {{ $sparepart->brand ?? '-' }}
                    </td>

                    <td class="p-5 text-right font-black text-red-600 text-base">
                        <span class="text-xs font-bold text-red-400 mr-1">Rp</span>{{ number_format($sparepart->price, 0, ',', '.') }}
                    </td>

                    <td class="p-5 text-center">
                        @if($sparepart->stock == 0)
                            <span class="px-3 py-1.5 bg-red-50 text-red-600 border border-red-100 rounded-lg text-xs font-black inline-flex items-center gap-1.5 shadow-sm">
                                <i class="fas fa-times-circle"></i> Habis
                            </span>
                        @elseif($sparepart->stock <= 5)
                            <span class="px-3 py-1.5 bg-red-50 text-red-600 border border-red-100 rounded-lg text-xs font-black inline-flex items-center gap-1.5 shadow-sm">
                                <i class="fas fa-exclamation-triangle"></i> {{ $sparepart->stock }}
                            </span>
                        @else
                            <span class="px-3 py-1.5 bg-green-50 text-green-700 border border-green-200 rounded-lg text-xs font-black shadow-sm">
                                {{ $sparepart->stock }}
                            </span>
                        @endif
                    </td>

                    <td class="p-5 text-center flex items-center justify-center gap-2">
                        <a href="{{ route('admin.spareparts.edit', $sparepart->id) }}" class="inline-flex items-center justify-center w-10 h-10 bg-white border border-gray-200 text-blue-500 hover:bg-blue-500 hover:text-white hover:border-blue-500 rounded-xl transition-all shadow-sm" title="Edit Data">
                            <i class="fas fa-edit"></i>
                        </a>
                        
                        <form action="{{ route('admin.spareparts.destroy', $sparepart->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus suku cadang ini? (Pastikan barang ini belum pernah masuk ke nota pembelian/servis)')">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center w-10 h-10 bg-white border border-gray-200 text-red-500 hover:bg-red-500 hover:text-white hover:border-red-500 rounded-xl transition-all shadow-sm" title="Hapus Barang">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-12 text-center">
                        <div class="w-20 h-20 bg-gray-50 border border-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                            <i class="fas fa-box-open text-3xl"></i>
                        </div>
                        <p class="text-gray-500 font-bold text-lg">Data barang tidak ditemukan.</p>
                        @if(request('search') || request('category'))
                            <p class="text-gray-400 text-sm mt-1">Coba gunakan kata kunci atau kategori yang lain.</p>
                            <a href="{{ route('admin.spareparts.index') }}" class="inline-block mt-4 text-blue-500 font-bold hover:underline">Reset Filter</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($spareparts->hasPages())
        <div class="p-5 border-t border-gray-100 bg-gray-50/50">
            {{ $spareparts->withQueryString()->links() }}
        </div>
    @endif
</div>

<script>
    let activeDropdownMenu = null;

    function toggleCustomDropdown(trigger) {
        const menu = trigger.nextElementSibling;
        const arrow = trigger.querySelector('.arrow-icon');

        if (activeDropdownMenu && activeDropdownMenu !== menu) {
            closeAllDropdowns();
        }

        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            if(arrow) arrow.classList.add('rotate-180', 'text-blue-500');
            activeDropdownMenu = menu;
        } else {
            closeAllDropdowns();
        }
    }

    function closeAllDropdowns() {
        document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('hidden'));
        document.querySelectorAll('.arrow-icon').forEach(arrow => arrow.classList.remove('rotate-180', 'text-blue-500'));
        activeDropdownMenu = null;
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-dropdown')) {
            closeAllDropdowns();
        }
    });

    function selectFilterOption(optionElement, value, labelText) {
        const container = optionElement.closest('.custom-dropdown');
        container.querySelector('input[type="hidden"]').value = value;
        container.querySelector('.selected-label').innerText = labelText;
        
        // Auto submit form when category is selected
        document.getElementById('filterForm').submit();
    }
</script>
@endsection