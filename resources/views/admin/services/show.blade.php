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
                    {{ $service->vehicle->license_plate }}
                </span>
                <p class="text-lg font-bold text-gray-800">{{ $service->vehicle->brand }} {{ $service->vehicle->model }}</p>
                <p class="text-sm text-gray-500">{{ $service->vehicle->color }}</p>
            </div>
            
            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center bg-gray-50 p-2 rounded-lg">
                    <span class="text-gray-500"><i class="fas fa-user text-gray-400 w-4 text-center mr-1"></i> Pelanggan:</span>
                    <span class="font-bold text-gray-800">{{ $service->vehicle->customer->name }}</span>
                </div>
                <div class="flex justify-between items-center bg-green-50 p-2 rounded-lg">
                    <span class="text-gray-500"><i class="fab fa-whatsapp text-green-500 w-4 text-center mr-1"></i> WhatsApp:</span>
                    <span class="font-bold text-green-700">{{ $service->vehicle->customer->phone_number }}</span>
                </div>
                <div class="flex justify-between items-center bg-gray-50 p-2 rounded-lg mt-3">
                    <span class="text-gray-500"><i class="fas fa-wrench text-gray-400 w-4 text-center mr-1"></i> Mekanik:</span>
                    <span class="font-bold text-gray-800">{{ $service->mechanic->name ?? 'Belum Ditentukan' }}</span>
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

            <form action="{{ route('admin.services.updateStatus', $service->id) }}" method="POST">
                @csrf
                @method('PUT')
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Update Status Servis:</label>
                
                <div class="relative z-30 mb-4">
                    <input type="hidden" name="status" id="hidden_status" value="{{ $service->status }}" required>

                    @php
                        $statusText = 'Menunggu Antrean';
                        $statusIcon = 'fa-clock text-yellow-500';
                        $statusBg = 'bg-yellow-50 border-yellow-200';
                        if($service->status == 'processing') {
                            $statusText = 'Sedang Dikerjakan';
                            $statusIcon = 'fa-tools text-blue-500';
                            $statusBg = 'bg-blue-50 border-blue-200';
                        } elseif($service->status == 'finished') {
                            $statusText = 'Selesai (Kirim WA)';
                            $statusIcon = 'fa-check-double text-green-500';
                            $statusBg = 'bg-green-50 border-green-200';
                        }
                    @endphp

                    <div id="trigger_status" class="w-full flex items-center justify-between px-4 py-3 {{ $statusBg }} border rounded-xl cursor-pointer transition-all" onclick="toggleCustomDropdown('menu_status', 'trigger_status')">
                        <div class="flex items-center gap-2">
                            <i id="icon_display_status" class="fas {{ $statusIcon }}"></i>
                            <span id="label_status" class="font-bold text-gray-800">{{ $statusText }}</span>
                        </div>
                        <i id="chevron_status" class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300"></i>
                    </div>

                    <div id="menu_status" class="hidden absolute top-full left-0 w-full mt-1 bg-white border border-gray-100 rounded-xl shadow-xl overflow-hidden z-50">
                        <div class="py-1">
                            <div class="px-4 py-3 hover:bg-yellow-50 cursor-pointer transition-colors flex items-center gap-3 border-b border-gray-50" onclick="selectStatusOption('pending', 'Menunggu Antrean', 'fa-clock', 'text-yellow-500', 'bg-yellow-50', 'border-yellow-200')">
                                <i class="fas fa-clock text-yellow-500 w-5 text-center"></i><span class="font-bold text-gray-700">Menunggu Antrean</span>
                            </div>
                            <div class="px-4 py-3 hover:bg-blue-50 cursor-pointer transition-colors flex items-center gap-3 border-b border-gray-50" onclick="selectStatusOption('processing', 'Sedang Dikerjakan', 'fa-tools', 'text-blue-500', 'bg-blue-50', 'border-blue-200')">
                                <i class="fas fa-tools text-blue-500 w-5 text-center"></i><span class="font-bold text-gray-700">Sedang Dikerjakan</span>
                            </div>
                            <div class="px-4 py-3 hover:bg-green-50 cursor-pointer transition-colors flex items-center gap-3" onclick="selectStatusOption('finished', 'Selesai (Kirim WA)', 'fa-check-double', 'text-green-500', 'bg-green-50', 'border-green-200')">
                                <i class="fas fa-check-double text-green-500 w-5 text-center"></i><span class="font-bold text-gray-700">Selesai (Kirim WA)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-gray-900 hover:bg-gray-800 text-white py-3 rounded-xl font-bold transition shadow-lg flex items-center justify-center gap-2">
                    <i class="fas fa-sync-alt"></i> Terapkan Status
                </button>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-6">
        
        @if(session('error'))
            <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-xl mb-4 font-medium flex items-center gap-2 shadow-sm">
                <i class="fas fa-exclamation-circle text-red-500"></i> {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded-xl mb-4 font-medium flex items-center gap-2 shadow-sm">
                <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden relative z-20">
            <div class="p-6 border-b bg-gray-50/50 flex justify-between items-center">
                <h3 class="font-black text-gray-800 text-lg tracking-tight"><i class="fas fa-file-invoice-dollar text-red-500 mr-2"></i>Rincian Nota Servis</h3>
            </div>

            <div class="p-6 border-b border-gray-100 bg-white">
                <form action="{{ route('admin.services.addSparepart', $service->id) }}" method="POST" class="flex flex-col sm:flex-row gap-4 items-end">
                    @csrf
                    
                    <div class="flex-1 relative z-20">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Pilih Suku Cadang Ditambahkan</label>
                        
                        <input type="hidden" name="sparepart_id" id="hidden_sparepart_id" required>

                        <div id="trigger_sparepart" class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-100 transition-all" onclick="toggleCustomDropdown('menu_sparepart', 'trigger_sparepart')">
                            <span id="label_sparepart" class="font-bold text-gray-400 select-none truncate">-- Cari & Pilih Barang --</span>
                            <i id="chevron_sparepart" class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300 ml-2"></i>
                        </div>

                        <div id="menu_sparepart" class="hidden absolute top-full left-0 w-full mt-1 bg-white border border-gray-100 rounded-xl shadow-xl overflow-hidden z-50">
                            <div class="max-h-60 overflow-y-auto custom-scrollbar py-1">
                                @foreach($spareparts as $sp)
                                    <div class="px-4 py-3 hover:bg-red-50 cursor-pointer transition-colors border-b border-gray-50 last:border-0" 
                                         onclick="selectSparepartOption('{{ $sp->id }}', '{{ addslashes($sp->name) }} (Rp {{ number_format($sp->price, 0, ',', '.') }})')">
                                        <p class="font-bold text-gray-800">{{ $sp->name }}</p>
                                        <div class="flex justify-between items-center mt-1">
                                            <span class="text-xs font-bold text-red-600">Rp {{ number_format($sp->price, 0, ',', '.') }}</span>
                                            <span class="text-xs font-medium bg-gray-100 px-2 py-0.5 rounded text-gray-500">Stok: {{ $sp->stock }}</span>
                                        </div>
                                    </div>
                                @endforeach
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

            <div class="p-6 relative z-10">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse mb-6">
                        <thead>
                            <tr class="border-b-2 border-gray-100 text-xs uppercase tracking-wider text-gray-400">
                                <th class="py-3 font-bold">Item / Keterangan</th>
                                <th class="py-3 font-bold text-center">Qty</th>
                                <th class="py-3 font-bold text-right">Harga Satuan</th>
                                <th class="py-3 font-bold text-right pr-4">Subtotal</th>
                                <th class="py-3"></th>
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
                                </td>
                                <td></td>
                            </tr>

                            @forelse($service->details as $detail)
                            <tr class="hover:bg-gray-50 transition group">
                                <td class="py-4 px-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-gray-300 group-hover:bg-red-400 transition"></div>
                                        <span class="font-bold text-gray-700 group-hover:text-gray-900 transition">{{ $detail->sparepart->name }}</span>
                                    </div>
                                </td>
                                <td class="py-4 text-center font-black text-gray-800 bg-gray-50 rounded-lg mx-2 my-2 inline-block px-3">{{ $detail->quantity }}</td>
                                <td class="py-4 text-right font-medium text-gray-500">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                <td class="py-4 text-right pr-4 font-black text-gray-900">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                <td class="py-4 text-center">
                                    <form action="{{ route('admin.services.removeSparepart', ['id' => $service->id, 'detail_id' => $detail->id]) }}" method="POST" class="inline" onsubmit="return confirm('Hapus suku cadang ini dari nota? Stok akan dikembalikan otomatis.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-300 hover:text-red-500 hover:bg-red-50 w-8 h-8 rounded-lg flex items-center justify-center transition" title="Hapus Item">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400 text-sm font-medium italic">
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

    function toggleCustomDropdown(menuId, triggerId) {
        const menu = document.getElementById(menuId);
        const trigger = document.getElementById(triggerId);
        const icon = document.getElementById(triggerId.replace('trigger_', 'chevron_'));

        if (activeDropdownMenu === menu) {
            closeDropdowns();
            return;
        }
        closeDropdowns();

        menu.classList.remove('hidden');
        if(!triggerId.includes('status')) { // Pengecualian styling untuk dropdown status
            trigger.classList.add('border-red-300', 'ring-4', 'ring-red-50', 'bg-white');
            trigger.classList.remove('border-gray-200', 'bg-gray-50');
        }
        if(icon) icon.classList.add('rotate-180', 'text-red-500');
        
        activeDropdownMenu = menu;
        activeDropdownTrigger = trigger;
    }

    function closeDropdowns() {
        if (activeDropdownMenu) {
            activeDropdownMenu.classList.add('hidden');
            
            if(!activeDropdownTrigger.id.includes('status')) {
                activeDropdownTrigger.classList.remove('border-red-300', 'ring-4', 'ring-red-50', 'bg-white');
                activeDropdownTrigger.classList.add('border-gray-200', 'bg-gray-50');
            }
            
            const icon = document.getElementById(activeDropdownTrigger.id.replace('trigger_', 'chevron_'));
            if(icon) icon.classList.remove('rotate-180', 'text-red-500');

            activeDropdownMenu = null;
            activeDropdownTrigger = null;
        }
    }

    // Menutup dropdown jika user mengklik area di luar dropdown
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


    // --- 3. SCRIPT KHUSUS OPSI STATUS ---
    function selectStatusOption(value, label, iconClass, iconColorClass, bgColorClass, borderColorClass) {
        document.getElementById('hidden_status').value = value;
        
        const trigger = document.getElementById('trigger_status');
        const labelElement = document.getElementById('label_status');
        const iconDisplay = document.getElementById('icon_display_status');

        // Ubah Label
        labelElement.innerText = label;

        // Reset & Set Ikon Baru
        iconDisplay.className = `fas ${iconClass} ${iconColorClass}`;

        // Reset & Set Warna Latar Trigger Baru
        trigger.className = `w-full flex items-center justify-between px-4 py-3 border rounded-xl cursor-pointer transition-all ${bgColorClass} ${borderColorClass}`;

        closeDropdowns();
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