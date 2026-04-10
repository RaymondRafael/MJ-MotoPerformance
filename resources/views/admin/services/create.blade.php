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

    <form action="{{ route('admin.services.store') }}" method="POST" class="space-y-8">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <div class="relative z-20">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Plat Kendaraan Pelanggan <span class="text-red-500">*</span></label>
                
                <input type="hidden" name="vehicle_id" id="hidden_vehicle_id" required>

                <div id="trigger_vehicle" class="w-full flex items-center justify-between px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl cursor-pointer hover:bg-gray-100 transition-all group" onclick="toggleCustomDropdown('menu_vehicle', 'trigger_vehicle')">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-200 text-gray-500 flex items-center justify-center group-hover:bg-white transition-colors">
                            <i class="fas fa-motorcycle text-sm"></i>
                        </div>
                        <span id="label_vehicle" class="text-gray-400 font-semibold select-none">Pilih Kendaraan...</span>
                    </div>
                    <i id="icon_vehicle" class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300"></i>
                </div>

                <div id="menu_vehicle" class="hidden absolute top-full left-0 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden z-50">
                    <div class="max-h-64 overflow-y-auto custom-scrollbar py-2">
                        @foreach($vehicles as $vehicle)
                            <div class="px-5 py-3 hover:bg-red-50 cursor-pointer transition-colors group border-b border-gray-50 last:border-0 flex items-center gap-3" 
                                 onclick="selectOption('vehicle', '{{ $vehicle->id }}', '{{ $vehicle->license_plate }} ({{ $vehicle->customer->name }})')">
                                <div class="w-2 h-2 rounded-full bg-gray-300 group-hover:bg-red-500 transition-colors"></div>
                                <div>
                                    <p class="font-bold text-gray-800 group-hover:text-red-600 transition-colors tracking-widest">{{ $vehicle->license_plate }}</p>
                                    <p class="text-xs text-gray-500 font-medium">{{ $vehicle->customer->name }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="relative z-10">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Mekanik Bertugas <span class="text-red-500">*</span></label>
                
                <input type="hidden" name="mechanic_id" id="hidden_mechanic_id" required>

                <div id="trigger_mechanic" class="w-full flex items-center justify-between px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl cursor-pointer hover:bg-gray-100 transition-all group" onclick="toggleCustomDropdown('menu_mechanic', 'trigger_mechanic')">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-200 text-gray-500 flex items-center justify-center group-hover:bg-white transition-colors">
                            <i class="fas fa-wrench text-sm"></i>
                        </div>
                        <span id="label_mechanic" class="text-gray-400 font-semibold select-none">Pilih Mekanik...</span>
                    </div>
                    <i id="icon_mechanic" class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300"></i>
                </div>

                <div id="menu_mechanic" class="hidden absolute top-full left-0 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden z-50">
                    <div class="max-h-64 overflow-y-auto custom-scrollbar py-2">
                        @foreach($mechanics as $mechanic)
                            <div class="px-5 py-3 hover:bg-red-50 cursor-pointer transition-colors group flex items-center gap-3" 
                                 onclick="selectOption('mechanic', '{{ $mechanic->id }}', '{{ $mechanic->name }}')">
                                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 group-hover:bg-red-100 group-hover:text-red-500 transition-colors">
                                    <i class="fas fa-user-tie text-xs"></i>
                                </div>
                                <span class="font-bold text-gray-800 group-hover:text-red-600 transition-colors">{{ $mechanic->name }}</span>
                            </div>
                        @endforeach
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
                    class="w-full pl-12 pr-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-red-50 focus:border-red-500 transition-all outline-none resize-none font-medium text-gray-800"></textarea>
            </div>
        </div>

        <input type="hidden" name="status" value="pending">

        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100 mt-10">
            <a href="{{ route('admin.services.index') }}" class="flex items-center gap-2 px-6 py-3.5 rounded-xl text-gray-500 font-bold hover:bg-gray-100 transition-colors">
                <i class="fas fa-times"></i> Batal
            </a>
            <button type="submit" class="flex items-center gap-2 px-8 py-3.5 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-500 hover:to-red-400 text-white rounded-xl font-bold transition-transform transform hover:-translate-y-1 shadow-lg shadow-red-500/30">
                <i class="fas fa-paper-plane"></i> Mulai Antrean
            </button>
        </div>
    </form>
</div>

<script>
    // Menyimpan state menu mana yang sedang terbuka
    let activeDropdownMenu = null;
    let activeDropdownTrigger = null;

    function toggleCustomDropdown(menuId, triggerId) {
        const menu = document.getElementById(menuId);
        const trigger = document.getElementById(triggerId);
        const icon = trigger.querySelector('.fa-chevron-down');

        // Jika mengklik menu yang sama, tutup menu tersebut
        if (activeDropdownMenu === menu) {
            closeDropdowns();
            return;
        }

        // Jika ada menu lain yang terbuka, tutup dulu
        closeDropdowns();

        // Buka menu yang baru diklik
        menu.classList.remove('hidden');
        trigger.classList.add('border-red-400', 'ring-4', 'ring-red-50', 'bg-white');
        trigger.classList.remove('border-gray-200', 'bg-gray-50');
        icon.classList.add('rotate-180', 'text-red-500');
        
        activeDropdownMenu = menu;
        activeDropdownTrigger = trigger;
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

            activeDropdownMenu = null;
            activeDropdownTrigger = null;
        }
    }

    function selectOption(type, value, label) {
        // 1. Masukkan nilai ke input tersembunyi
        document.getElementById(`hidden_${type}_id`).value = value;
        
        // 2. Ubah teks label di kotak dropdown
        const labelElement = document.getElementById(`label_${type}`);
        labelElement.innerText = label;
        labelElement.classList.remove('text-gray-400');
        labelElement.classList.add('text-gray-900'); // Teks jadi lebih gelap saat sudah dipilih

        // 3. Ubah warna ikon di sebelah kiri label
        const triggerDiv = document.getElementById(`trigger_${type}`);
        const iconBox = triggerDiv.children[0].children[0]; // Akses div kotak ikon
        iconBox.classList.remove('bg-gray-200', 'text-gray-500');
        iconBox.classList.add('bg-red-100', 'text-red-600');

        // 4. Tutup dropdown
        closeDropdowns();
    }

    // Menutup dropdown jika user mengklik area di luar dropdown
    document.addEventListener('click', function(event) {
        if (activeDropdownMenu && !activeDropdownTrigger.contains(event.target) && !activeDropdownMenu.contains(event.target)) {
            closeDropdowns();
        }
    });
</script>

@endsection