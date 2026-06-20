@extends('admin.layouts.app')
@section('title', 'Data Suku Cadang')
@section('header', 'Inventaris Suku Cadang')

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

@if (session('error'))
    <div class="mb-6 p-5 bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-sm flex items-start gap-4 transition-all">
        <div class="mt-0.5 text-red-500 text-xl"><i class="fas fa-exclamation-circle"></i></div>
        <div>
            <h3 class="text-sm font-bold text-red-800 mb-1">Aksi Ditolak:</h3>
            <p class="text-sm text-red-700">{{ session('error') }}</p>
        </div>
    </div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-4">
    <div class="p-6 border-b flex flex-col md:flex-row justify-between items-center bg-gray-50 gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white border border-gray-200 text-gray-600 rounded-lg flex items-center justify-center shadow-sm">
                <i class="fas fa-cogs"></i>
            </div>
            <h2 class="font-bold text-gray-800 text-lg tracking-tight">Daftar Stok Suku Cadang</h2>
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
            <form action="{{ route('admin.spareparts.index') }}" method="GET" id="filterForm" class="flex flex-col sm:flex-row w-full md:w-auto gap-3">
                
                <div class="relative custom-dropdown w-full sm:w-52" data-type="category-filter">
                    <input type="hidden" name="category" id="filter_category" value="{{ request('category') }}">
                    
                    <div class="dropdown-trigger w-full pl-11 pr-10 py-2.5 bg-white border border-gray-300 rounded-lg flex items-center justify-between cursor-pointer hover:border-red-300 focus:ring-4 focus:ring-red-50 transition-all group shadow-sm" onclick="toggleCustomDropdown(this)">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-tags text-xs"></i>
                        </div>
                        <span class="text-sm font-bold text-gray-700 selected-label truncate">
                            {{ request('category') ?: '-- Semua Kategori --' }}
                        </span>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400 transition-transform duration-300 arrow-icon">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>

                    <div class="dropdown-menu hidden absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-xl overflow-hidden max-h-60 overflow-y-auto custom-scrollbar">
                        <div class="p-1.5 space-y-1">
                            <div class="px-4 py-2.5 hover:bg-gray-50 cursor-pointer rounded-lg transition-colors font-bold text-gray-500 text-xs" onclick="selectFilterOption(this, '', '-- Semua Kategori --')">
                                -- Semua Kategori --
                            </div>
                            @foreach($categories as $cat)
                                <div class="px-4 py-2.5 hover:bg-red-50 cursor-pointer rounded-lg transition-colors font-bold text-gray-800 hover:text-red-600 text-xs flex items-center justify-between" onclick="selectFilterOption(this, '{{ $cat }}', '{{ $cat }}')">
                                    {{ $cat }}
                                    @if(request('category') == $cat)
                                        <i class="fas fa-check-circle text-red-500"></i>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex w-full md:w-auto shadow-sm rounded-lg">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau merek..." 
                        class="px-4 py-2.5 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-red-100 focus:border-red-400 outline-none text-sm w-full md:w-64 font-medium transition-all">
                    <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-5 py-2.5 rounded-r-lg border border-l-0 border-gray-300 transition-colors">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            
            @if(request('search') || request('category'))
                <a href="{{ route('admin.spareparts.index') }}" class="bg-white border border-gray-300 text-gray-500 hover:text-red-500 hover:bg-red-50 px-4 py-2.5 rounded-lg transition-colors shadow-sm" title="Reset Pencarian">
                    <i class="fas fa-sync-alt"></i>
                </a>
            @endif
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white border-b border-gray-100 text-xs uppercase tracking-wider text-gray-400">
                    <th class="p-5 font-bold text-center w-16">No.</th> 
                    <th class="p-5 font-bold">Nama Suku Cadang & Kategori</th>
                    <th class="p-5 font-bold">Merek</th>
                    <th class="p-5 font-bold">Harga Jual</th>
                    <th class="p-5 font-bold">Stok Tersedia</th>
                    <th class="p-5 font-bold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 divide-y divide-gray-50">
                @forelse($spareparts as $sparepart)
                <tr class="hover:bg-gray-50/80 transition-colors group">
                    <td class="p-5 font-bold text-gray-900 text-center">
                        {{ ($spareparts->currentPage() - 1) * $spareparts->perPage() + $loop->iteration }}
                    </td>
                    <td class="p-5 font-bold text-gray-900">
                        {{ $sparepart->name }}<br>
                        <span class="text-[10px] uppercase tracking-wider bg-gray-100 border border-gray-200 text-gray-600 px-2 py-0.5 rounded-md font-bold mt-1.5 inline-block">
                            {{ $sparepart->category ?? 'Lainnya' }}
                        </span>
                    </td>
                    <td class="p-5 text-gray-600 font-medium">
                        {{ $sparepart->brand ?? '-' }}
                    </td>
                    <td class="p-5 text-red-600 font-black">
                        <span class="text-xs text-red-400 mr-1">Rp</span>{{ number_format($sparepart->price, 0, ',', '.') }}
                    </td>
                    <td class="p-5">
                        <span class="px-3.5 py-1.5 rounded-lg text-xs font-black shadow-sm {{ $sparepart->stock > 5 ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-red-50 border border-red-200 text-red-700' }}">
                            {{ $sparepart->stock }} Pcs
                        </span>
                    </td>
                    <td class="p-5 text-center">
                        <form action="{{ route('admin.spareparts.destroy', $sparepart->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus suku cadang ini? (Pastikan barang ini belum pernah masuk ke nota pembelian/servis)')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-600 p-2.5 hover:bg-red-50 rounded-xl transition-all" title="Hapus Barang">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-10 text-center">
                        <div class="w-16 h-16 bg-gray-50 border border-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3 text-gray-300">
                            <i class="fas fa-box-open text-2xl"></i>
                        </div>
                        <p class="text-gray-500 font-bold">Tidak ada data suku cadang ditemukan.</p>
                        @if(request('search') || request('category'))
                            <p class="text-gray-400 text-xs mt-1">Coba gunakan kata kunci atau kategori yang lain.</p>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($spareparts->hasPages())
        <div class="p-5 border-t border-gray-100 bg-gray-50">
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
            if(arrow) arrow.classList.add('rotate-180', 'text-red-500');
            activeDropdownMenu = menu;
        } else {
            closeAllDropdowns();
        }
    }

    function closeAllDropdowns() {
        document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('hidden'));
        document.querySelectorAll('.arrow-icon').forEach(arrow => arrow.classList.remove('rotate-180', 'text-red-500'));
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
        
        // Kirim form secara otomatis saat kategori diklik
        document.getElementById('filterForm').submit();
    }
</script>
@endsection