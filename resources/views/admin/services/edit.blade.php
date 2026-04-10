@extends('admin.layouts.app')
@section('title', 'Edit Servis')
@section('header', 'Edit Transaksi Servis')

@section('content')

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Edit Informasi Servis</h1>
            <p class="text-sm text-gray-500 mt-1">Perbarui detail mekanik atau keluhan kendaraan pelanggan.</p>
        </div>
        <a href="{{ route('admin.services.index') }}" class="flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 hover:text-red-600 transition-all font-bold shadow-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-5 bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-sm flex items-start gap-4">
            <div class="mt-0.5 text-red-500 text-xl"><i class="fas fa-exclamation-circle"></i></div>
            <div>
                <h3 class="text-sm font-bold text-red-800 mb-1">Terdapat kesalahan pengisian:</h3>
                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden">
        
        <form action="{{ route('admin.services.update', $service->id) }}" method="POST" class="p-8 space-y-8">
            @csrf
            @method('PUT')

            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 border-b pb-2">Informasi Kendaraan (Tidak dapat diubah)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nama Pelanggan</label>
                        <div class="w-full bg-gray-50 border border-gray-200 rounded-2xl cursor-not-allowed flex items-center px-4 py-3 min-h-[52px]">
                            <i class="fas fa-user text-gray-400 mr-3 flex-shrink-0 text-center w-4"></i>
                            <span class="flex-grow text-gray-500 font-bold select-none pr-2 break-words leading-tight">
                                {{ $service->vehicle->customer->name ?? 'Tidak diketahui' }}
                            </span>
                            <i class="fas fa-lock text-gray-300 flex-shrink-0"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kendaraan (Plat)</label>
                        <div class="w-full bg-gray-50 border border-gray-200 rounded-2xl cursor-not-allowed flex items-center px-4 py-3 min-h-[52px]">
                            <i class="fas fa-motorcycle text-gray-400 mr-3 flex-shrink-0 text-center w-4"></i>
                            <span class="flex-grow text-gray-500 font-bold select-none pr-2 break-words leading-tight">
                                {{ $service->vehicle->brand }} {{ $service->vehicle->model }} ({{ $service->vehicle->license_plate }})
                            </span>
                            <i class="fas fa-lock text-gray-300 flex-shrink-0"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 border-b pb-2">Detail Pengerjaan</h3>
                
                <div class="space-y-6">
                    <div class="relative z-20">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Mekanik Bertugas <span class="text-red-500">*</span></label>
                        
                        @php
                            $currentMechanicId = old('mechanic_id', $service->mechanic_id);
                            $currentMechanic = $mechanics->firstWhere('id', $currentMechanicId);
                            $currentMechanicName = $currentMechanic ? $currentMechanic->name : 'Pilih Mekanik...';
                        @endphp

                        <input type="hidden" name="mechanic_id" id="hidden_mechanic_id" value="{{ $currentMechanicId }}" required>

                        <div id="trigger_mechanic" class="w-full flex items-center justify-between px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl cursor-pointer hover:bg-gray-100 transition-all group" onclick="toggleCustomDropdown('menu_mechanic', 'trigger_mechanic')">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg {{ $currentMechanic ? 'bg-red-100 text-red-600' : 'bg-gray-200 text-gray-500' }} flex items-center justify-center group-hover:bg-white transition-colors" id="icon_box_mechanic">
                                    <i class="fas fa-wrench text-sm"></i>
                                </div>
                                <span id="label_mechanic" class="font-bold select-none {{ $currentMechanic ? 'text-gray-900' : 'text-gray-400' }}">{{ $currentMechanicName }}</span>
                            </div>
                            <i id="icon_mechanic" class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300"></i>
                        </div>

                        <div id="menu_mechanic" class="hidden absolute top-full left-0 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden z-50">
                            <div class="max-h-64 overflow-y-auto custom-scrollbar py-2">
                                @foreach($mechanics as $mechanic)
                                    <div class="px-5 py-3 hover:bg-red-50 cursor-pointer transition-colors group flex items-center justify-between" 
                                         onclick="selectOption('mechanic', '{{ $mechanic->id }}', '{{ $mechanic->name }}')">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 group-hover:bg-red-100 group-hover:text-red-500 transition-colors">
                                                <i class="fas fa-user-tie text-xs"></i>
                                            </div>
                                            <span class="font-bold text-gray-800 group-hover:text-red-600 transition-colors">{{ $mechanic->name }}</span>
                                        </div>
                                        @if($currentMechanicId == $mechanic->id)
                                            <i class="fas fa-check-circle text-green-500" id="check_{{ $mechanic->id }}"></i>
                                        @else
                                            <i class="fas fa-check-circle text-green-500 hidden" id="check_{{ $mechanic->id }}"></i>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="relative z-0">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Keluhan / Catatan Kendaraan <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute top-4 left-5 text-gray-400">
                                <i class="fas fa-comment-dots"></i>
                            </div>
                            <textarea name="complaint" required rows="4" 
                                class="w-full pl-12 pr-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-red-50 focus:border-red-500 transition-all outline-none resize-none font-medium text-gray-800">{{ old('complaint', $service->complaint) }}</textarea>
                        </div>
                        <p class="text-xs text-gray-400 mt-2 ml-1"><i class="fas fa-info-circle mr-1"></i> Revisi detail keluhan pelanggan jika ada perubahan diagnosa awal.</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100 mt-4">
                <a href="{{ route('admin.services.index') }}" class="px-6 py-3.5 rounded-xl text-gray-500 font-bold hover:bg-gray-100 transition-colors">
                    Batal
                </a>
                <button type="submit" class="flex items-center gap-2 px-8 py-3.5 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-500 hover:to-red-400 text-white rounded-xl font-bold transition-transform transform hover:-translate-y-1 shadow-lg shadow-red-500/30">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
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
        labelElement.classList.add('text-gray-900', 'font-bold');

        // 3. Ubah warna ikon trigger
        const iconBox = document.getElementById(`icon_box_${type}`);
        iconBox.classList.remove('bg-gray-200', 'text-gray-500');
        iconBox.classList.add('bg-red-100', 'text-red-600');

        // 4. (Opsional) Memindahkan ceklis hijau ke mekanik yang baru dipilih
        // Sembunyikan semua ceklis
        document.querySelectorAll('[id^="check_"]').forEach(el => el.classList.add('hidden'));
        // Munculkan ceklis pada mekanik terpilih
        const selectedCheck = document.getElementById(`check_${value}`);
        if(selectedCheck) selectedCheck.classList.remove('hidden');

        // 5. Tutup dropdown
        closeDropdowns();
    }

    document.addEventListener('click', function(event) {
        if (activeDropdownMenu && !activeDropdownTrigger.contains(event.target) && !activeDropdownMenu.contains(event.target)) {
            closeDropdowns();
        }
    });
</script>

@endsection