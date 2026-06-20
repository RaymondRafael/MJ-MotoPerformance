@extends('admin.layouts.app')
@section('title', 'Buka Servis Baru')
@section('header', 'Transaksi Servis')

@section('content')

<style>
    /* Menyembunyikan scrollbar bawaan namun tetap bisa discroll */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-8 max-w-4xl mx-auto mt-4">
    
    <div class="mb-8 flex items-center border-b border-gray-100 pb-5">
        <div class="w-12 h-12 bg-red-50 text-red-600 rounded-xl flex items-center justify-center text-xl mr-4 shadow-inner">
            <i class="fas fa-clipboard-check"></i>
        </div>
        <div>
            <h2 class="text-2xl font-black text-gray-900 tracking-tight">Form Buka Antrean Servis</h2>
            <p class="text-gray-500 text-sm mt-1">Masukkan data kendaraan pelanggan dan keluhan utama.</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm animate-fade-in-down">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-bold text-red-800">Formulir tidak lengkap!</h3>
                <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif
    <form action="{{ route('admin.services.store') }}" method="POST" class="space-y-8">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <div class="relative z-20">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Plat Kendaraan Pelanggan <span class="text-red-500">*</span></label>
                
                <input type="hidden" name="vehicle_id" id="hidden_vehicle_id" required>

                <div id="trigger_vehicle" class="w-full flex items-center justify-between px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl cursor-pointer hover:bg-gray-100 transition-all group" onclick="toggleCustomDropdown('menu_vehicle', 'trigger_vehicle', 'search_vehicle')">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-200 text-gray-500 flex items-center justify-center group-hover:bg-white transition-colors" id="icon_box_vehicle">
                            <i class="fas fa-motorcycle text-sm"></i>
                        </div>
                        <span id="label_vehicle" class="text-gray-400 font-semibold select-none">Pilih Kendaraan...</span>
                    </div>
                    <i id="icon_vehicle" class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300"></i>
                </div>

                <div id="menu_vehicle" class="hidden absolute top-full left-0 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden z-50">
                    
                    <div class="sticky top-0 bg-white border-b border-gray-100 p-3 z-10">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-search text-xs"></i>
                            </div>
                            <input type="text" id="search_vehicle" class="w-full pl-9 pr-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-gray-700 focus:ring-2 focus:ring-red-100 focus:border-red-400 outline-none transition-all" placeholder="Cari Plat atau Nama..." autocomplete="off" onclick="event.stopPropagation()">
                        </div>
                    </div>

                    <div class="max-h-64 overflow-y-auto custom-scrollbar py-2">
                        @foreach($vehicles as $vehicle)
                            <div class="vehicle-item px-5 py-3 hover:bg-red-50 cursor-pointer transition-colors group border-b border-gray-50 last:border-0 flex items-center gap-3" 
                                 onclick="selectOption('vehicle', '{{ $vehicle->id }}', '{{ $vehicle->license_plate }} ({{ addslashes($vehicle->customer->name) }})')">
                                <div class="w-2 h-2 rounded-full bg-gray-300 group-hover:bg-red-500 transition-colors"></div>
                                <div>
                                    <p class="font-bold text-gray-800 group-hover:text-red-600 transition-colors tracking-widest vehicle-plate">{{ $vehicle->license_plate }}</p>
                                    <p class="text-xs text-gray-500 font-medium vehicle-customer">{{ $vehicle->customer->name }}</p>
                                </div>
                            </div>
                        @endforeach

                        <div id="no_vehicle_found" class="hidden px-5 py-6 text-center">
                            <i class="fas fa-search-minus text-gray-300 text-2xl mb-2"></i>
                            <p class="text-sm font-bold text-gray-500">Kendaraan tidak ditemukan.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="relative z-10">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Mekanik Bertugas <span class="text-red-500">*</span></label>
                
                <input type="hidden" name="mechanic_id" id="hidden_mechanic_id" required>

                <div id="trigger_mechanic" class="w-full flex items-center justify-between px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl cursor-pointer hover:bg-gray-100 transition-all group" onclick="toggleCustomDropdown('menu_mechanic', 'trigger_mechanic', 'search_mechanic')">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-200 text-gray-500 flex items-center justify-center group-hover:bg-white transition-colors" id="icon_box_mechanic">
                            <i class="fas fa-wrench text-sm"></i>
                        </div>
                        <span id="label_mechanic" class="text-gray-400 font-semibold select-none">Pilih Mekanik...</span>
                    </div>
                    <i id="icon_mechanic" class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300"></i>
                </div>

                <div id="menu_mechanic" class="hidden absolute top-full left-0 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden z-50">
                    
                    <div class="sticky top-0 bg-white border-b border-gray-100 p-3 z-10">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-search text-xs"></i>
                            </div>
                            <input type="text" id="search_mechanic" class="w-full pl-9 pr-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-gray-700 focus:ring-2 focus:ring-red-100 focus:border-red-400 outline-none transition-all" placeholder="Cari Nama Mekanik..." autocomplete="off" onclick="event.stopPropagation()">
                        </div>
                    </div>

                    <div class="max-h-64 overflow-y-auto custom-scrollbar py-2">
                        @foreach($mechanics as $mechanic)
                            <div class="mechanic-item px-5 py-3 hover:bg-red-50 cursor-pointer transition-colors group flex items-center gap-3 border-b border-gray-50 last:border-0" 
                                 onclick="selectOption('mechanic', '{{ $mechanic->id }}', '{{ addslashes($mechanic->name) }}')">
                                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 group-hover:bg-red-100 group-hover:text-red-500 transition-colors">
                                    <i class="fas fa-user-tie text-xs"></i>
                                </div>
                                <span class="font-bold text-gray-800 group-hover:text-red-600 transition-colors mechanic-name">{{ $mechanic->name }}</span>
                            </div>
                        @endforeach

                        <div id="no_mechanic_found" class="hidden px-5 py-6 text-center">
                            <i class="fas fa-search-minus text-gray-300 text-2xl mb-2"></i>
                            <p class="text-sm font-bold text-gray-500">Mekanik tidak ditemukan.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="relative z-0">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Keluhan / Catatan Awal <span class="text-red-500">*</span></label>
            <div class="relative">
                <div class="absolute top-4 left-5 text-gray-400">
                    <i class="fas fa-comment-dots"></i>
                </div>
                <textarea name="complaint" required rows="4" placeholder="Contoh: Tarikan gas berat, minta ganti oli gardan, dan cek rem depan..."
                    class="w-full pl-12 pr-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-red-50 focus:border-red-500 transition-all outline-none resize-none font-medium text-gray-800">{{ old('complaint') }}</textarea>
            </div>
        </div>

        <input type="hidden" name="status" value="pending">

        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100 mt-10">
            <a href="{{ route('admin.services.index') }}" class="flex items-center gap-2 px-6 py-3.5 rounded-xl text-gray-500 font-bold hover:bg-gray-100 transition-colors">
                <i class="fas fa-times"></i> Batal
            </a>
            <button type="submit" class="flex items-center gap-2 px-8 py-3.5 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white rounded-xl font-bold transition-transform transform hover:-translate-y-1 shadow-lg shadow-blue-500/30">
                <i class="fas fa-paper-plane"></i> Mulai Antrean
            </button>
        </div>
    </form>
</div>

<script>
    let activeDropdownMenu = null;
    let activeDropdownTrigger = null;

    // Fungsi Utama Dropdown
    function toggleCustomDropdown(menuId, triggerId, searchInputId) {
        const menu = document.getElementById(menuId);
        const trigger = document.getElementById(triggerId);
        const icon = trigger.querySelector('.fa-chevron-down');
        const searchInput = document.getElementById(searchInputId);

        if (activeDropdownMenu === menu) {
            closeDropdowns();
            return;
        }

        closeDropdowns();

        menu.classList.remove('hidden');
        trigger.classList.add('border-red-400', 'ring-4', 'ring-red-50', 'bg-white');
        trigger.classList.remove('border-gray-200', 'bg-gray-50');
        icon.classList.add('rotate-180', 'text-red-500');
        
        activeDropdownMenu = menu;
        activeDropdownTrigger = trigger;

        // Fokus ke kotak pencarian
        if(searchInput) {
            setTimeout(() => searchInput.focus(), 50);
        }
    }

    function closeDropdowns() {
        if (activeDropdownMenu) {
            activeDropdownMenu.classList.add('hidden');
            activeDropdownTrigger.classList.remove('border-red-400', 'ring-4', 'ring-red-50', 'bg-white');
            activeDropdownTrigger.classList.add('border-gray-200', 'bg-gray-50');
            
            const icon = activeDropdownTrigger.querySelector('.fa-chevron-down');
            if(icon) {
                icon.classList.remove('rotate-180', 'text-red-500');
            }

            // Bersihkan input teks kendaraan & mekanik saat menu tertutup
            resetSearch('vehicle');
            resetSearch('mechanic');

            activeDropdownMenu = null;
            activeDropdownTrigger = null;
        }
    }

    function selectOption(type, value, label) {
        document.getElementById(`hidden_${type}_id`).value = value;
        
        const labelElement = document.getElementById(`label_${type}`);
        labelElement.innerText = label;
        labelElement.classList.remove('text-gray-400');
        labelElement.classList.add('text-gray-900', 'font-black');

        const triggerDiv = document.getElementById(`trigger_${type}`);
        const iconBox = triggerDiv.children[0].children[0]; 
        iconBox.classList.remove('bg-gray-200', 'text-gray-500');
        iconBox.classList.add('bg-red-100', 'text-red-600');

        closeDropdowns();
    }

    // --- LOGIKA LIVE SEARCH ---
    
    // Pencarian Kendaraan
    document.getElementById('search_vehicle').addEventListener('input', function(e) {
        filterDropdown(e.target.value, '.vehicle-item', ['vehicle-plate', 'vehicle-customer'], 'no_vehicle_found');
    });

    // Pencarian Mekanik
    document.getElementById('search_mechanic').addEventListener('input', function(e) {
        filterDropdown(e.target.value, '.mechanic-item', ['mechanic-name'], 'no_mechanic_found');
    });

    // Fungsi Filter Umum
    function filterDropdown(keyword, itemSelector, textClasses, noFoundId) {
        const query = keyword.toLowerCase();
        const items = document.querySelectorAll(itemSelector);
        let hasVisibleItem = false;

        items.forEach(item => {
            let itemText = '';
            // Gabungkan teks dari class yang ingin dicari (misal: plat nomor + nama customer)
            textClasses.forEach(cls => {
                const element = item.querySelector('.' + cls);
                if(element) itemText += element.innerText.toLowerCase() + ' ';
            });

            if (itemText.includes(query)) {
                item.classList.remove('hidden');
                item.classList.add('flex');
                hasVisibleItem = true;
            } else {
                item.classList.remove('flex');
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

    function resetSearch(type) {
        const input = document.getElementById(`search_${type}`);
        if (input) {
            input.value = '';
            filterDropdown('', `.${type}-item`, [], `no_${type}_found`);
        }
    }

    // Tutup jika area luar di klik
    document.addEventListener('click', function(event) {
        if (activeDropdownMenu && !activeDropdownTrigger.contains(event.target) && !activeDropdownMenu.contains(event.target)) {
            closeDropdowns();
        }
    });
</script>

@endsection