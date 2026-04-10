@extends('admin.layouts.app')
@section('title', 'Tambah Kendaraan')
@section('header', 'Manajemen Kendaraan')

@section('content')

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-8 max-w-4xl mx-auto mt-4">
    
    <div class="mb-8 flex items-center border-b border-gray-100 pb-5">
        <div class="w-12 h-12 bg-red-50 text-red-600 rounded-xl flex items-center justify-center text-xl mr-4 shadow-inner">
            <i class="fas fa-motorcycle"></i>
        </div>
        <div>
            <h2 class="text-2xl font-black text-gray-900 tracking-tight">Form Tambah Kendaraan</h2>
            <p class="text-gray-500 text-sm mt-1">Daftarkan data kendaraan baru milik pelanggan ke dalam sistem.</p>
        </div>
    </div>

    <form action="{{ route('admin.vehicles.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="relative z-30">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Pemilik Kendaraan <span class="text-red-500">*</span></label>
            
            <input type="hidden" name="customer_id" id="hidden_customer_id" required>

            <div id="trigger_customer" class="w-full flex items-center justify-between px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl cursor-pointer hover:bg-gray-100 transition-all group" onclick="toggleCustomDropdown('menu_customer', 'trigger_customer')">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-200 text-gray-500 flex items-center justify-center group-hover:bg-white transition-colors" id="icon_box_customer">
                        <i class="fas fa-user text-sm"></i>
                    </div>
                    <span id="label_customer" class="text-gray-400 font-semibold select-none">Pilih Nama Pelanggan...</span>
                </div>
                <i id="icon_customer" class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300"></i>
            </div>

            <div id="menu_customer" class="hidden absolute top-full left-0 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden z-50">
                <div class="max-h-60 overflow-y-auto custom-scrollbar py-2">
                    @foreach($customers as $customer)
                        <div class="px-5 py-3 hover:bg-red-50 cursor-pointer transition-colors group border-b border-gray-50 last:border-0 flex items-center gap-3" 
                             onclick="selectOption('customer', '{{ $customer->id }}', '{{ $customer->name }}')">
                            <div class="w-2 h-2 rounded-full bg-gray-300 group-hover:bg-red-500 transition-colors"></div>
                            <div>
                                <p class="font-bold text-gray-800 group-hover:text-red-600 transition-colors">{{ $customer->name }}</p>
                                <p class="text-xs text-gray-500 font-medium"><i class="fab fa-whatsapp mr-1"></i> {{ $customer->phone_number }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="relative z-20">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Plat Nomor Kendaraan <span class="text-red-500">*</span></label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                    <i class="fas fa-id-badge"></i>
                </div>
                <input type="text" name="license_plate" required placeholder="Contoh: D 1234 ABC"
                    class="w-full pl-11 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-red-50 focus:border-red-500 transition-all outline-none font-black text-gray-800 uppercase tracking-widest">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative z-10">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Merek <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-industry"></i>
                    </div>
                    <input type="text" name="brand" required placeholder="Contoh: Honda"
                        class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-red-50 focus:border-red-500 transition-all outline-none font-bold text-gray-800 capitalize">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Model <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-motorcycle"></i>
                    </div>
                    <input type="text" name="model" required placeholder="Contoh: Vario 150"
                        class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-red-50 focus:border-red-500 transition-all outline-none font-bold text-gray-800 capitalize">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Warna</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-palette"></i>
                    </div>
                    <input type="text" name="color" placeholder="Contoh: Hitam Matte"
                        class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-red-50 focus:border-red-500 transition-all outline-none font-bold text-gray-800 capitalize">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100 mt-10">
            <a href="{{ route('admin.vehicles.index') }}" class="flex items-center gap-2 px-6 py-3.5 rounded-xl text-gray-500 font-bold hover:bg-gray-100 transition-colors">
                <i class="fas fa-times"></i> Batal
            </a>
            <button type="submit" class="flex items-center gap-2 px-8 py-3.5 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-500 hover:to-red-400 text-white rounded-xl font-bold transition-transform transform hover:-translate-y-1 shadow-lg shadow-red-500/30">
                <i class="fas fa-save"></i> Simpan Data
            </button>
        </div>
    </form>
</div>

<script>
    let activeDropdownMenu = null;
    let activeDropdownTrigger = null;

    function toggleCustomDropdown(menuId, triggerId) {
        const menu = document.getElementById(menuId);
        const trigger = document.getElementById(triggerId);
        const icon = trigger.querySelector('.fa-chevron-down');

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
        labelElement.classList.add('text-gray-900', 'font-black');

        // 3. Ubah warna ikon di sebelah kiri label
        const iconBox = document.getElementById(`icon_box_${type}`);
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