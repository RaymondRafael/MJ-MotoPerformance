@extends('admin.layouts.app')

@section('title', 'Transaksi Servis')
@section('header', 'Manajemen Servis')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    
    <div class="p-6 border-b flex flex-col xl:flex-row justify-between items-center bg-gray-50 gap-4 rounded-t-2xl relative z-30">
        <h2 class="font-bold text-gray-700 w-full xl:w-auto text-center xl:text-left">Antrean Kendaraan Hari Ini</h2>
        
        <div class="flex flex-col md:flex-row items-center gap-3 w-full xl:w-auto">
            <form action="{{ route('admin.services.index') }}" method="GET" id="filterForm" class="flex flex-wrap md:flex-nowrap items-center gap-3 w-full md:w-auto justify-center">
                
                <input type="hidden" name="month" id="filterMonth" value="{{ request('month') }}">
                <input type="hidden" name="year" id="filterYear" value="{{ request('year') }}">
                <input type="hidden" name="status" id="filterStatus" value="{{ request('status') }}">

                <div class="relative w-full sm:w-auto" id="dropdown-container-month">
                    <div id="dropdown-trigger-month" onclick="toggleFilterDropdown('month')" class="flex items-center w-full sm:w-auto bg-white rounded-xl px-4 py-2 border border-gray-200 hover:border-gray-300 transition-all cursor-pointer select-none min-w-[160px] h-[52px]">
                        <i class="fas fa-calendar-alt text-gray-500 mr-3 text-lg"></i>
                        <div class="flex flex-col flex-grow pr-4 text-left">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider -mb-1">Pilih Bulan</span>
                            <span id="dropdown-label-month" class="text-sm font-bold text-gray-800 truncate max-w-[100px]">
                                {{ request('month') && isset($months[request('month')]) ? $months[request('month')] : 'Semua Bulan' }}
                            </span>
                        </div>
                        <i id="dropdown-icon-month" class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300"></i>
                    </div>

                    <div id="dropdown-menu-month" class="hidden absolute top-full left-0 mt-2 w-full min-w-[180px] bg-white border border-gray-100 rounded-xl shadow-xl overflow-hidden py-2 z-50">
                        <div class="max-h-60 overflow-y-auto custom-scrollbar">
                            <div class="dropdown-item-month px-4 py-2.5 text-sm font-semibold cursor-pointer transition-colors flex items-center justify-between {{ !request('month') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" onclick="selectFilterOption('month', '', 'Semua Bulan', this)">
                                <span>Semua Bulan</span>
                                <i class="fas fa-check text-xs {{ !request('month') ? '' : 'hidden' }} text-red-500"></i>
                            </div>
                            @foreach($months as $angka => $nama)
                                <div class="dropdown-item-month px-4 py-2.5 text-sm font-semibold cursor-pointer transition-colors flex items-center justify-between {{ request('month') == $angka ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" onclick="selectFilterOption('month', '{{ $angka }}', '{{ $nama }}', this)">
                                    <span>{{ $nama }}</span>
                                    <i class="fas fa-check text-xs {{ request('month') == $angka ? '' : 'hidden' }} text-red-500"></i>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="relative w-full sm:w-auto" id="dropdown-container-year">
                    <div id="dropdown-trigger-year" onclick="toggleFilterDropdown('year')" class="flex items-center w-full sm:w-auto bg-white rounded-xl px-4 py-2 border border-gray-200 hover:border-gray-300 transition-all cursor-pointer select-none min-w-[140px] h-[52px]">
                        <i class="fas fa-calendar-check text-gray-500 mr-3 text-lg"></i>
                        <div class="flex flex-col flex-grow pr-4 text-left">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider -mb-1">Pilih Tahun</span>
                            <span id="dropdown-label-year" class="text-sm font-bold text-gray-800 truncate max-w-[80px]">
                                {{ request('year') ?: 'Semua Tahun' }}
                            </span>
                        </div>
                        <i id="dropdown-icon-year" class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300"></i>
                    </div>

                    <div id="dropdown-menu-year" class="hidden absolute top-full left-0 mt-2 w-full min-w-[140px] bg-white border border-gray-100 rounded-xl shadow-xl overflow-hidden py-2 z-50">
                        <div class="max-h-60 overflow-y-auto custom-scrollbar">
                            <div class="dropdown-item-year px-4 py-2.5 text-sm font-semibold cursor-pointer transition-colors flex items-center justify-between {{ !request('year') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" onclick="selectFilterOption('year', '', 'Semua Tahun', this)">
                                <span>Semua Tahun</span>
                                <i class="fas fa-check text-xs {{ !request('year') ? '' : 'hidden' }} text-red-500"></i>
                            </div>
                            @foreach($years as $yr)
                                <div class="dropdown-item-year px-4 py-2.5 text-sm font-semibold cursor-pointer transition-colors flex items-center justify-between {{ request('year') == $yr ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" onclick="selectFilterOption('year', '{{ $yr }}', '{{ $yr }}', this)">
                                    <span>{{ $yr }}</span>
                                    <i class="fas fa-check text-xs {{ request('year') == $yr ? '' : 'hidden' }} text-red-500"></i>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="relative w-full sm:w-auto" id="dropdown-container-status">
                    <div id="dropdown-trigger-status" onclick="toggleFilterDropdown('status')" class="flex items-center w-full sm:w-auto bg-white rounded-xl px-4 py-2 border border-gray-200 hover:border-gray-300 transition-all cursor-pointer select-none min-w-[140px] h-[52px]">
                        <i class="fas fa-tasks text-gray-500 mr-3 text-lg"></i>
                        <div class="flex flex-col flex-grow pr-4 text-left">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider -mb-1">Status</span>
                            <span id="dropdown-label-status" class="text-sm font-bold text-gray-800 truncate max-w-[80px]">
                                @if(request('status') == 'pending') Menunggu
                                @elseif(request('status') == 'processing') Dikerjakan
                                @elseif(request('status') == 'finished') Selesai
                                @elseif(request('status') == 'lunas') Lunas
                                @else Semua Status
                                @endif
                            </span>
                        </div>
                        <i id="dropdown-icon-status" class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300"></i>
                    </div>

                    <div id="dropdown-menu-status" class="hidden absolute top-full left-0 mt-2 w-full min-w-[140px] bg-white border border-gray-100 rounded-xl shadow-xl overflow-hidden py-2 z-50">
                        <div class="max-h-60 overflow-y-auto custom-scrollbar">
                            <div class="dropdown-item-status px-4 py-2.5 text-sm font-semibold cursor-pointer transition-colors flex items-center justify-between {{ !request('status') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" onclick="selectFilterOption('status', '', 'Semua Status', this)">
                                <span>Semua Status</span>
                                <i class="fas fa-check text-xs {{ !request('status') ? '' : 'hidden' }} text-red-500"></i>
                            </div>
                            <div class="dropdown-item-status px-4 py-2.5 text-sm font-semibold cursor-pointer transition-colors flex items-center justify-between {{ request('status') == 'pending' ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" onclick="selectFilterOption('status', 'pending', 'Menunggu', this)">
                                <span>Menunggu</span>
                                <i class="fas fa-check text-xs {{ request('status') == 'pending' ? '' : 'hidden' }} text-red-500"></i>
                            </div>
                            <div class="dropdown-item-status px-4 py-2.5 text-sm font-semibold cursor-pointer transition-colors flex items-center justify-between {{ request('status') == 'processing' ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" onclick="selectFilterOption('status', 'processing', 'Dikerjakan', this)">
                                <span>Dikerjakan</span>
                                <i class="fas fa-check text-xs {{ request('status') == 'processing' ? '' : 'hidden' }} text-red-500"></i>
                            </div>
                            <div class="dropdown-item-status px-4 py-2.5 text-sm font-semibold cursor-pointer transition-colors flex items-center justify-between {{ request('status') == 'finished' ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" onclick="selectFilterOption('status', 'finished', 'Selesai', this)">
                                <span>Selesai</span>
                                <i class="fas fa-check text-xs {{ request('status') == 'finished' ? '' : 'hidden' }} text-red-500"></i>
                            </div>
                            <div class="dropdown-item-status px-4 py-2.5 text-sm font-semibold cursor-pointer transition-colors flex items-center justify-between {{ request('status') == 'lunas' ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" onclick="selectFilterOption('status', 'lunas', 'Lunas', this)">
                                <span>Lunas</span>
                                <i class="fas fa-check text-xs {{ request('status') == 'lunas' ? '' : 'hidden' }} text-red-500"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Plat/Nama/Keluhan..." 
                        class="px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-red-500 focus:border-red-500 text-sm w-full md:w-64">
                    <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-r-lg border border-l-0 border-gray-300 transition">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                @if(request()->filled('month') || request()->filled('year') || request()->filled('search') || request()->filled('status'))
                    <a href="{{ route('admin.services.index') }}" 
                    class="bg-white hover:bg-gray-100 text-gray-600 hover:text-red-500 px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium transition whitespace-nowrap shadow-sm h-[38px] flex items-center justify-center" 
                    title="Bersihkan Semua Filter">
                        <i class="fas fa-undo mr-1"></i> Reset
                    </a>
                @endif
            </form>

            <a href="{{ route('admin.services.create') }}" class="bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-bold transition whitespace-nowrap h-[38px] flex items-center justify-center">
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
                    <th class="p-4 font-bold text-center w-16">No.</th> 
                    <th class="p-4 font-bold">Plat Nomor</th>
                    <th class="p-4 font-bold">Pelanggan</th>
                    <th class="p-4 font-bold">Keluhan & Mekanik</th>
                    <th class="p-4 font-bold">Status</th>
                    <th class="p-4 font-bold text-center">Aksi Lanjutan</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                @forelse($services as $service)
                <tr class="hover:bg-gray-50 transition">
                    
                    <td class="p-4 font-bold text-gray-900 text-center">
                        {{ ($services->currentPage() - 1) * $services->perPage() + $loop->iteration }}
                    </td>
                    
                    <td class="p-4">
                        @if($service->vehicle)
                            <span class="font-bold text-gray-900">{{ $service->vehicle->license_plate }}</span><br>
                            <span class="text-xs text-gray-500">
                                {{ $service->vehicle->brand }} {{ $service->vehicle->model }}
                            </span>
                        @else
                            <span class="font-bold text-gray-400 line-through" title="Data kendaraan telah dihapus">
                                Data Dihapus
                            </span><br>
                            <span class="text-xs text-gray-400 line-through" title="Data kendaraan telah dihapus">
                                Tidak Diketahui
                            </span>
                        @endif
                    </td>

                    <td class="p-4">
                        @if($service->vehicle && $service->vehicle->customer)
                            <span class="text-gray-900">{{ $service->vehicle->customer->name }}</span><br>
                            <span class="text-xs text-gray-500"><i class="fab fa-whatsapp text-green-500"></i> 
                                {{ $service->vehicle->customer->phone_number }}
                            </span>
                        @else
                            <span class="text-gray-400 line-through" title="Data pelanggan telah dihapus">
                                Pelanggan Dihapus
                            </span><br>
                            <span class="text-xs text-gray-400 line-through" title="Data pelanggan telah dihapus">
                                <i class="fab fa-whatsapp text-gray-400"></i> -
                            </span>
                        @endif
                    </td>

                    <td class="p-4 max-w-xs" title="{{ $service->complaint }}">
                        <div class="truncate mb-1.5">{{ $service->complaint }}</div>
                        
                        <div>
                            @if($service->mechanic)
                                <span class="text-[10px] text-blue-600 font-bold bg-blue-50 px-2 py-0.5 rounded border border-blue-100">
                                    <i class="fas fa-wrench mr-1"></i> {{ $service->mechanic->name }}
                                </span>
                            @elseif($service->historical_mechanic_name)
                                <span class="text-[10px] text-gray-400 font-bold bg-gray-100 px-2 py-0.5 rounded border border-gray-200 line-through" title="Mekanik telah dihapus">
                                    <i class="fas fa-wrench mr-1"></i> {{ $service->historical_mechanic_name }}
                                </span>
                            @else
                                <span class="text-[10px] text-red-500 font-bold bg-red-50 px-2 py-0.5 rounded border border-red-100">
                                    Belum Ditentukan
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="p-4">
                        @if($service->status == 'pending')
                            <span class="px-3 py-1.5 rounded-lg text-xs font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">Menunggu</span>
                        @elseif($service->status == 'processing')
                            <span class="px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200">Dikerjakan</span>
                        @elseif($service->status == 'finished')
                            <span class="px-3 py-1.5 rounded-lg text-xs font-bold bg-green-100 text-green-700 border border-green-200">Selesai (Siap Ambil)</span>
                        @elseif($service->status == 'lunas')
                            <span class="px-3 py-1.5 rounded-lg text-xs font-bold bg-purple-100 text-purple-700 border border-purple-200"><i class="fas fa-check-double mr-1"></i> Lunas</span>
                        @endif
                    </td>
                    <td class="p-4 text-center space-x-2 flex justify-center items-center">

                        <a href="{{ route('admin.services.show', $service->id) }}" class="text-blue-500 hover:text-blue-700 p-2 bg-blue-50 rounded-lg ml-1" title="Lihat Detail/Nota"><i class="fas fa-eye"></i></a>
                        
                        @if(!in_array($service->status, ['finished', 'lunas']))
                            <a href="{{ route('admin.services.edit', $service->id) }}" class="text-gray-500 hover:text-gray-700 p-2 bg-gray-100 rounded-lg" title="Edit Antrean"><i class="fas fa-edit"></i></a>
                        @endif
                        
                        @if(!in_array($service->status, ['finished', 'lunas']))
                            <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin membatalkan dan menghapus antrean servis ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 p-2 bg-red-50 rounded-lg" title="Batalkan/Hapus Antrean"><i class="fas fa-trash"></i></button>
                            </form>
                        @else
                            <button type="button" class="text-gray-400 p-2 bg-gray-100 rounded-lg cursor-not-allowed" title="Nota ini sudah dikunci permanen oleh sistem keuangan">
                                <i class="fas fa-lock"></i>
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-gray-400">Tidak ada data servis yang ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($services->hasPages())
        <div class="p-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
            {{ $services->withQueryString()->links() }}
        </div>
    @endif
</div>

<script>
    function toggleFilterDropdown(tipe) {
        const menu = document.getElementById(`dropdown-menu-${tipe}`);
        const trigger = document.getElementById(`dropdown-trigger-${tipe}`);
        const icon = document.getElementById(`dropdown-icon-${tipe}`);

        // Tutup dropdown lain saat satu dibuka
        if(tipe === 'month') { closeFilterDropdown('year'); closeFilterDropdown('status'); }
        if(tipe === 'year') { closeFilterDropdown('month'); closeFilterDropdown('status'); }
        if(tipe === 'status') { closeFilterDropdown('month'); closeFilterDropdown('year'); }

        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            icon.classList.add('rotate-180', 'text-red-500');
            trigger.classList.add('border-red-300', 'ring-2', 'ring-red-100', 'bg-white');
        } else {
            closeFilterDropdown(tipe);
        }
    }

    function closeFilterDropdown(tipe) {
        const menu = document.getElementById(`dropdown-menu-${tipe}`);
        const trigger = document.getElementById(`dropdown-trigger-${tipe}`);
        const icon = document.getElementById(`dropdown-icon-${tipe}`);

        if (menu && !menu.classList.contains('hidden')) {
            menu.classList.add('hidden');
            icon.classList.remove('rotate-180', 'text-red-500');
            trigger.classList.remove('border-red-300', 'ring-2', 'ring-red-100', 'bg-white');
        }
    }

    function selectFilterOption(tipe, value, label, element) {
        document.getElementById(`filter${tipe.charAt(0).toUpperCase() + tipe.slice(1)}`).value = value;
        document.getElementById(`dropdown-label-${tipe}`).innerText = label;

        const allItems = document.querySelectorAll(`.dropdown-item-${tipe}`);
        allItems.forEach(item => {
            item.classList.remove('bg-red-50', 'text-red-600');
            item.classList.add('text-gray-600', 'hover:bg-gray-50', 'hover:text-gray-900');
            item.querySelector('.fa-check').classList.add('hidden');
        });

        element.classList.add('bg-red-50', 'text-red-600');
        element.classList.remove('text-gray-600', 'hover:bg-gray-50', 'hover:text-gray-900');
        element.querySelector('.fa-check').classList.remove('hidden');

        closeFilterDropdown(tipe);
        document.getElementById('filterForm').submit();
    }

    // Klik di luar area dropdown untuk menutup semua dropdown termasuk status
    document.addEventListener('click', function(event) {
        if (!event.target.closest('#dropdown-container-month')) closeFilterDropdown('month');
        if (!event.target.closest('#dropdown-container-year')) closeFilterDropdown('year');
        if (!event.target.closest('#dropdown-container-status')) closeFilterDropdown('status');
    });
</script>
@endsection