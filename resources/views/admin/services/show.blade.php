@extends('admin.layouts.app')
@section('title', 'Detail Nota Servis')
@section('header', 'Detail Transaksi Servis')

@section('content')

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('admin.services.index') }}" class="text-gray-500 hover:text-red-600 font-bold transition flex items-center gap-2 px-4 py-2 bg-white rounded-lg border border-gray-200 shadow-sm hover:bg-gray-50">
        <i class="fas fa-arrow-left"></i> Kembali ke Antrean
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <div class="lg:col-span-1 space-y-6">
        
        <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-red-600 to-red-400"></div>
            
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 border-b pb-2">Informasi Kendaraan</h3>
            <div class="text-center mb-6">
                <span class="inline-block px-4 py-2 bg-gray-900 text-white text-xl font-black rounded-lg tracking-widest mb-2 shadow-inner">
                    {{ $service->historical_license_plate ?? ($service->vehicle->license_plate ?? 'PLAT DIHAPUS') }}
                </span>
                
                <p class="text-lg font-bold text-gray-800">
                    {{ $service->historical_vehicle_motor ?? ($service->vehicle ? $service->vehicle->brand . ' ' . $service->vehicle->model : 'Kendaraan Tidak Diketahui') }}
                </p>
                
                <p class="text-sm text-gray-500">
                    {{ $service->vehicle->color ?? 'Warna Tidak Tercatat' }}
                </p>
            </div>
            
            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center bg-gray-50 p-2 rounded-lg">
                    <span class="text-gray-500"><i class="fas fa-user text-gray-400 w-4 text-center mr-1"></i> Pelanggan:</span>
                    <span class="font-bold text-gray-800">
                        {{ $service->historical_customer_name ?? ($service->vehicle->customer->name ?? 'Pelanggan Dihapus') }}
                    </span>
                </div>
                
                <div class="flex justify-between items-center bg-green-50 p-2 rounded-lg">
                    <span class="text-gray-500"><i class="fab fa-whatsapp text-green-500 w-4 text-center mr-1"></i> WhatsApp:</span>
                    <span class="font-bold text-green-700">
                        {{ $service->historical_customer_phone ?? ($service->vehicle->customer->phone_number ?? '-') }}
                    </span>
                </div>
                
                <div class="flex justify-between items-center bg-gray-50 p-2 rounded-lg mt-3">
                    <span class="text-gray-500"><i class="fas fa-wrench text-gray-400 w-4 text-center mr-1"></i> Mekanik:</span>
                    
                    @if($service->mechanic)
                        <span class="font-bold text-gray-800">{{ $service->mechanic->name }}</span>
                    @elseif($service->historical_mechanic_name)
                        <div class="text-right">
                            <span class="font-bold text-gray-400 line-through" title="Mekanik ini sudah tidak ada di sistem">{{ $service->historical_mechanic_name }}</span>
                            <span class="text-[10px] text-red-500 font-bold ml-1 uppercase"><i class="fas fa-exclamation-triangle"></i> Dihapus</span>
                        </div>
                    @else
                        <span class="font-bold text-gray-800">Belum Ditentukan</span>
                    @endif
                </div>

                <div class="flex justify-between items-center bg-gray-50 p-2 rounded-lg">
                    <span class="text-gray-500"><i class="far fa-calendar-alt text-gray-400 w-4 text-center mr-1"></i> Tanggal Masuk:</span>
                    <span class="font-bold text-gray-800">{{ $service->created_at->format('d M Y, H:i') }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-6">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 border-b pb-2">Keluhan & Status Pengerjaan</h3>
            <p class="text-sm text-gray-700 bg-red-50 p-4 rounded-xl border border-red-100 mb-6 font-medium italic relative">
                <i class="fas fa-quote-left absolute top-2 left-2 text-red-200 text-2xl z-0"></i>
                <span class="relative z-10">"{{ $service->complaint }}"</span>
            </p>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Status Saat Ini:</label>
                
                @if($service->status == 'pending')
                    <div class="w-full flex items-center justify-between px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-xl">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-clock text-yellow-500 text-lg"></i>
                            <span class="font-bold text-gray-800">Menunggu Antrean</span>
                        </div>
                    </div>
                @elseif($service->status == 'processing')
                    <div class="w-full flex items-center justify-between px-4 py-3 bg-blue-50 border border-blue-200 rounded-xl">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-tools text-blue-500 text-lg"></i>
                            <span class="font-bold text-gray-800">Sedang Dikerjakan</span>
                        </div>
                    </div>
                @elseif($service->status == 'finished')
                    <div class="w-full flex items-center justify-between px-4 py-3 bg-green-50 border border-green-200 rounded-xl">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-check-double text-green-500 text-lg"></i>
                            <span class="font-bold text-gray-800">Selesai (Siap Ambil)</span>
                        </div>
                    </div>
                @elseif($service->status == 'lunas')
                    <div class="w-full flex items-center justify-between px-4 py-3 bg-purple-50 border border-purple-200 rounded-xl">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-hand-holding-usd text-purple-500 text-lg"></i>
                            <span class="font-bold text-gray-800">Pembayaran Lunas</span>
                        </div>
                    </div>
                @endif
                
                <p class="text-[10px] text-gray-400 mt-3 font-medium text-center italic">*Status dikendalikan secara otomatis oleh sistem.</p>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-6">
        
        @if(session('error'))
            <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-xl mb-4 font-medium flex items-center gap-2 shadow-sm animate-fade-in-down">
                <i class="fas fa-exclamation-circle text-red-500"></i> {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded-xl mb-4 font-medium flex items-center gap-2 shadow-sm animate-fade-in-down">
                <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden relative z-20">
            <div class="p-6 border-b bg-gray-50/50 flex justify-between items-center">
                <h3 class="font-black text-gray-800 text-lg tracking-tight"><i class="fas fa-file-invoice-dollar text-red-500 mr-2"></i>Rincian Nota Servis</h3>
            </div>

            @if(!in_array($service->status, ['finished', 'lunas']))
                <div class="p-6 border-b border-gray-100 bg-white">
                    <form action="{{ route('admin.services.addSparepart', $service->id) }}" method="POST" class="flex flex-col sm:flex-row gap-4 items-end">
                        @csrf
                        
                        <div class="flex-1 relative z-20">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Pilih Suku Cadang Ditambahkan</label>
                            
                            <input type="hidden" name="sparepart_id" id="hidden_sparepart_id" required>

                            <div id="trigger_sparepart" class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-100 transition-all" onclick="toggleCustomDropdown('menu_sparepart', 'trigger_sparepart', 'search_sparepart')">
                                <span id="label_sparepart" class="font-bold text-gray-400 select-none truncate">-- Cari & Pilih Barang --</span>
                                <i id="chevron_sparepart" class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300 ml-2"></i>
                            </div>

                            <div id="menu_sparepart" class="hidden absolute top-full left-0 w-full mt-1 bg-white border border-gray-100 rounded-xl shadow-xl overflow-hidden z-50">
                                <div class="sticky top-0 bg-white border-b border-gray-100 p-3 z-10">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                            <i class="fas fa-search text-xs"></i>
                                        </div>
                                        <input type="text" id="search_sparepart" class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-gray-700 focus:ring-2 focus:ring-red-100 focus:border-red-400 outline-none transition-all" placeholder="Cari nama atau kategori..." autocomplete="off" onclick="event.stopPropagation()">
                                    </div>
                                </div>

                                <div class="max-h-60 overflow-y-auto custom-scrollbar p-2 space-y-1">
                                    @foreach($spareparts as $sp)
                                        <div class="sparepart-item px-4 py-3 hover:bg-red-50 cursor-pointer transition-colors border-b border-gray-50 last:border-0" 
                                             onclick="selectSparepartOption('{{ $sp->id }}', '{{ addslashes($sp->name) }} (Rp {{ number_format($sp->price, 0, ',', '.') }})')">
                                            
                                            <div class="flex items-center gap-2 mb-0.5">
                                                <p class="font-bold text-gray-800 sparepart-name">{{ $sp->name }}</p>
                                                <span class="sparepart-category text-[9px] uppercase tracking-widest bg-gray-200 text-gray-600 px-1.5 py-0.5 rounded font-bold">{{ $sp->category ?? 'Lainnya' }}</span>
                                            </div>

                                            <div class="flex justify-between items-center mt-1">
                                                <span class="text-xs font-bold text-red-600">Rp {{ number_format($sp->price, 0, ',', '.') }}</span>
                                                <span class="text-xs font-medium bg-gray-100 px-2 py-0.5 rounded text-gray-500">Stok: {{ $sp->stock }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    <div id="no_sparepart_found" class="hidden px-5 py-6 text-center">
                                        <i class="fas fa-box-open text-gray-300 text-2xl mb-2"></i>
                                        <p class="text-sm font-bold text-gray-500">Barang tidak ditemukan.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="w-full sm:w-24">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 text-center">Qty</label>
                            <input type="number" name="quantity" value="1" min="1" required class="w-full text-center font-bold text-gray-800 rounded-xl border-gray-200 bg-gray-50 px-3 py-3 focus:bg-white focus:ring-2 focus:ring-red-200 focus:border-red-500 transition outline-none">
                        </div>
                        <button type="submit" class="w-full sm:w-auto bg-gray-900 hover:bg-gray-800 text-white px-6 py-3.5 rounded-xl font-bold transition shadow-md flex items-center justify-center gap-2">
                            <i class="fas fa-plus"></i> <span class="sm:hidden">Tambah</span>
                        </button>
                    </form>
                </div>
            @endif

            <div class="p-6 relative z-10">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse mb-6">
                        <thead>
                            <tr class="border-b-2 border-gray-100 text-xs uppercase tracking-wider text-gray-400">
                                <th class="py-3 font-bold">Item / Keterangan</th>
                                <th class="py-3 font-bold text-center">Qty</th>
                                <th class="py-3 font-bold text-right">Harga Satuan</th>
                                <th class="py-3 font-bold text-right pr-4">Subtotal</th>
                                @if(!in_array($service->status, ['finished', 'lunas']))
                                    <th class="py-3"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-700 divide-y divide-gray-50">
                            
                            <tr class="bg-blue-50/30">
                                <td class="py-4 px-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                                        <span class="font-bold text-gray-800">Biaya Jasa Mekanik</span>
                                    </div>
                                </td>
                                <td class="py-4 text-center font-medium text-gray-400">-</td>
                                <td class="py-4 text-right text-xs font-bold text-blue-500 bg-blue-50/50 rounded px-2">Input Manual</td>
                                <td class="py-4 text-right pr-4">
                                    @if(in_array($service->status, ['finished', 'lunas']))
                                        <span class="text-sm font-black text-gray-900">Rp {{ number_format($service->service_cost, 0, ',', '.') }}</span>
                                    @else
                                        <form action="{{ route('admin.services.updateCost', $service->id) }}" method="POST" class="flex justify-end items-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            <span class="text-xs font-bold text-gray-400 mt-0.5">Rp</span>
                                            <input type="hidden" name="service_cost" id="raw_service_cost" value="{{ $service->service_cost }}">
                                            <input type="text" id="formatted_service_cost" 
                                                value="{{ number_format($service->service_cost, 0, ',', '.') }}" 
                                                class="w-28 text-sm font-black text-right rounded-lg border-gray-200 bg-white py-1.5 px-2 focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none transition shadow-sm" 
                                                oninput="formatCurrency(this)">
                                            <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-sm" title="Simpan Jasa">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                                @if(!in_array($service->status, ['finished', 'lunas']))
                                    <td></td>
                                @endif
                            </tr>

                            @forelse($service->details as $detail)
                            <tr class="hover:bg-gray-50 transition group">
                                <td class="py-4 px-2">
                                    <div class="flex items-start gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-gray-300 mt-2 group-hover:bg-red-400 transition"></div>
                                        <div class="flex flex-col">
                                            @if($detail->sparepart)
                                                <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-3 mb-1">
                                                    <span class="font-bold text-gray-700 group-hover:text-gray-900 transition text-base">{{ $detail->sparepart->name }}</span>
                                                    <span class="w-max text-[9px] uppercase tracking-widest bg-gray-100 border border-gray-200 text-gray-600 px-2 py-0.5 rounded font-bold">
                                                        {{ $detail->sparepart->category ?? 'Lainnya' }}
                                                    </span>
                                                </div>
                                            @else
                                                <span class="font-bold text-gray-400 line-through transition" title="Nama Historis">{{ $detail->historical_name ?? 'Barang Telah Dihapus' }}</span>
                                                <span class="text-[10px] font-bold text-red-500 uppercase tracking-wider mt-0.5"><i class="fas fa-exclamation-triangle"></i> Dihapus dari Inventaris</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 text-center font-black text-gray-800 bg-gray-50 rounded-lg mx-2 my-2 inline-block px-3">{{ $detail->quantity }}</td>
                                <td class="py-4 text-right font-medium text-gray-500">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                <td class="py-4 text-right pr-4 font-black text-gray-900">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                
                                @if(!in_array($service->status, ['finished', 'lunas']))
                                    <td class="py-4 text-center">
                                        <form action="{{ route('admin.services.removeSparepart', ['id' => $service->id, 'detail_id' => $detail->id]) }}" method="POST" class="inline" onsubmit="return confirm('Hapus suku cadang ini dari nota? Jika barang master masih ada, stoknya akan dikembalikan otomatis.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-300 hover:text-red-500 hover:bg-red-50 w-8 h-8 rounded-lg flex items-center justify-center transition" title="Hapus Item">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ in_array($service->status, ['finished', 'lunas']) ? '4' : '5' }}" class="py-8 text-center text-gray-400 text-sm font-medium italic">
                                    Belum ada suku cadang yang ditambahkan.
                                </td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

                <div class="bg-gradient-to-r from-red-50 to-white p-5 rounded-2xl border border-red-100 flex justify-between items-center shadow-inner mt-4">
                    <span class="text-sm font-black text-red-800 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-calculator text-red-400"></i> Total Tagihan
                    </span>
                    <span class="text-4xl font-black text-red-600 tracking-tight">Rp {{ number_format($service->total_cost, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // --- 1. SCRIPT UNTUK CUSTOM DROPDOWN UMUM ---
    let activeDropdownMenu = null;
    let activeDropdownTrigger = null;

    function toggleCustomDropdown(menuId, triggerId, searchInputId) {
        const menu = document.getElementById(menuId);
        const trigger = document.getElementById(triggerId);
        const icon = document.getElementById(triggerId.replace('trigger_', 'chevron_'));
        const searchInput = document.getElementById(searchInputId);

        if (activeDropdownMenu === menu) {
            closeDropdowns();
            return;
        }
        closeDropdowns();

        menu.classList.remove('hidden');
        trigger.classList.add('border-red-300', 'ring-4', 'ring-red-50', 'bg-white');
        trigger.classList.remove('border-gray-200', 'bg-gray-50');
        
        if(icon) icon.classList.add('rotate-180', 'text-red-500');
        
        activeDropdownMenu = menu;
        activeDropdownTrigger = trigger;

        // Auto-focus ke search bar
        if (searchInput) {
            setTimeout(() => searchInput.focus(), 50);
        }
    }

    function closeDropdowns() {
        if (activeDropdownMenu) {
            activeDropdownMenu.classList.add('hidden');
            
            activeDropdownTrigger.classList.remove('border-red-300', 'ring-4', 'ring-red-50', 'bg-white');
            activeDropdownTrigger.classList.add('border-gray-200', 'bg-gray-50');
            
            const icon = document.getElementById(activeDropdownTrigger.id.replace('trigger_', 'chevron_'));
            if(icon) icon.classList.remove('rotate-180', 'text-red-500');

            // Reset Pencarian
            const searchInput = document.getElementById('search_sparepart');
            if (searchInput) {
                searchInput.value = '';
                filterDropdown('', '.sparepart-item', ['sparepart-name', 'sparepart-category'], 'no_sparepart_found');
            }

            activeDropdownMenu = null;
            activeDropdownTrigger = null;
        }
    }

    document.addEventListener('click', function(event) {
        if (activeDropdownMenu && !activeDropdownTrigger.contains(event.target) && !activeDropdownMenu.contains(event.target)) {
            closeDropdowns();
        }
    });

    // --- 2. SCRIPT KHUSUS OPSI SPAREPART ---
    function selectSparepartOption(value, label) {
        document.getElementById('hidden_sparepart_id').value = value;
        const labelElement = document.getElementById('label_sparepart');
        labelElement.innerText = label;
        labelElement.classList.remove('text-gray-400');
        labelElement.classList.add('text-gray-900');
        closeDropdowns();
    }

    // --- 3. LOGIKA LIVE SEARCH ---
    const searchSparepart = document.getElementById('search_sparepart');
    if(searchSparepart){
        searchSparepart.addEventListener('input', function(e) {
            // PERBAIKAN: Menambahkan 'sparepart-category' agar bisa dicari berdasarkan kategori
            filterDropdown(e.target.value, '.sparepart-item', ['sparepart-name', 'sparepart-category'], 'no_sparepart_found');
        });
    }

    function filterDropdown(keyword, itemSelector, textClasses, noFoundId) {
        const query = keyword.toLowerCase();
        const items = document.querySelectorAll(itemSelector);
        let hasVisibleItem = false;

        items.forEach(item => {
            let itemText = '';
            textClasses.forEach(cls => {
                const element = item.querySelector('.' + cls);
                if(element) itemText += element.innerText.toLowerCase() + ' ';
            });

            if (itemText.includes(query)) {
                item.classList.remove('hidden');
                item.style.display = 'block'; 
                hasVisibleItem = true;
            } else {
                item.style.display = 'none';
                item.classList.add('hidden');
            }
        });

        const noFoundMsg = document.getElementById(noFoundId);
        if (!hasVisibleItem && query !== '') {
            noFoundMsg.classList.remove('hidden');
        } else {
            noFoundMsg.classList.add('hidden');
        }
    }

    // --- 4. SCRIPT FORMAT MATA UANG ---
    function formatCurrency(input) {
        let rawValue = input.value.replace(/[^0-9]/g, '');
        document.getElementById('raw_service_cost').value = rawValue;
        if (rawValue) {
            input.value = parseInt(rawValue, 10).toLocaleString('id-ID');
        } else {
            input.value = '';
        }
    }
</script>

@endsection