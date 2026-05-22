@extends('admin.layouts.app')
@section('title', 'Catat Pembelian')
@section('header', 'Inventaris & Pembelian')

@section('content')

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-8 mt-4">
    
    <div class="mb-8 flex items-center justify-between border-b border-gray-100 pb-5">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl mr-4 shadow-inner">
                <i class="fas fa-boxes"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black text-gray-900 tracking-tight">Penerimaan Barang & Stok</h2>
                <p class="text-gray-500 text-sm mt-1">Stok akan otomatis terformat dengan titik agar mudah dibaca.</p>
            </div>
        </div>
        <a href="{{ route('admin.purchases.index') }}" class="hidden sm:flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 hover:text-blue-600 transition-all font-bold shadow-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="{{ route('admin.purchases.store') }}" method="POST" id="purchaseForm">
        @if (session('error'))
            <div class="mb-8 p-5 bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-sm flex items-start gap-4 fade-in-up">
                <div class="mt-0.5 text-red-500 text-xl"><i class="fas fa-exclamation-circle"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-red-800 mb-1">Gagal Menyimpan Data:</h3>
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        @endif
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10 bg-gray-50 p-6 rounded-2xl border border-gray-100 shadow-inner">
            <div class="relative">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Nama Supplier</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-truck-loading"></i>
                    </div>
                    <input type="text" name="supplier_name" required placeholder="Nama Toko / Distributor"
                        class="w-full pl-11 pr-4 py-3.5 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none font-bold text-gray-800 shadow-sm">
                </div>
            </div>
            <div class="relative">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tanggal Masuk</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <input type="date" name="purchase_date" required value="{{ date('Y-m-d') }}"
                        class="w-full pl-11 pr-4 py-3.5 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none font-bold text-gray-800 shadow-sm">
                </div>
            </div>
        </div>

        <div id="items_container" class="space-y-4">
            <div class="item-row bg-white border border-gray-100 rounded-2xl p-6 shadow-sm relative transition-all hover:border-blue-200 group/row z-10">
                <div class="flex flex-col lg:flex-row gap-6">
                    
                    <div class="w-full lg:w-[20%]">
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Kategori Barang</label>
                        <div class="relative custom-dropdown" data-type="mode">
                            <input type="hidden" name="items[0][mode]" class="mode-input" value="existing">
                            
                            <div class="dropdown-trigger w-full pl-11 pr-10 py-3.5 bg-blue-50/50 border border-blue-100 rounded-xl flex items-center justify-between cursor-pointer focus:ring-4 focus:ring-blue-100 transition-all group" onclick="toggleCustomDropdown(this)">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-blue-500 icon-left">
                                    <i class="fas fa-exchange-alt"></i>
                                </div>
                                <span class="text-sm font-black text-blue-700 selected-label">Barang Lama</span>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-blue-400 transition-transform duration-300 arrow-icon">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>

                            <div class="dropdown-menu hidden absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden">
                                <div class="p-2">
                                    <div class="px-4 py-3 hover:bg-blue-50 cursor-pointer rounded-xl transition-colors font-bold text-gray-800 hover:text-blue-600 flex items-center gap-3" onclick="selectCustomOption(this, 'existing', 'Barang Lama')">
                                        <div class="w-6 h-6 rounded-md bg-blue-100 text-blue-500 flex items-center justify-center"><i class="fas fa-box text-xs"></i></div> Barang Lama
                                    </div>
                                    <div class="px-4 py-3 hover:bg-green-50 cursor-pointer rounded-xl transition-colors font-bold text-gray-800 hover:text-green-600 flex items-center gap-3" onclick="selectCustomOption(this, 'new', 'Barang Baru')">
                                        <div class="w-6 h-6 rounded-md bg-green-100 text-green-500 flex items-center justify-center"><i class="fas fa-plus text-xs"></i></div> Barang Baru
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="w-full lg:w-[40%]">
                        
                        <div class="existing-fields">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Pilih Suku Cadang</label>
                            <div class="relative custom-dropdown" data-type="sparepart">
                                <input type="hidden" name="items[0][sparepart_id]" class="sparepart-input" value="">

                                <div class="dropdown-trigger w-full pl-11 pr-10 py-3.5 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-between cursor-pointer hover:bg-white transition-all group" onclick="toggleCustomDropdown(this)">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                        <i class="fas fa-cogs"></i>
                                    </div>
                                    <span class="text-sm font-bold text-gray-400 selected-label truncate">-- Pilih Suku Cadang --</span>
                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400 transition-transform duration-300 arrow-icon">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </div>
                                </div>

                                <div class="dropdown-menu hidden absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden">
                                    <div class="max-h-60 overflow-y-auto custom-scrollbar p-2 space-y-1">
                                        <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer rounded-xl transition-colors font-bold text-gray-400 flex items-center gap-3" onclick="selectCustomOption(this, '', '-- Pilih Suku Cadang --')">
                                            -- Batal Memilih --
                                        </div>
                                        @foreach($spareparts as $sp)
                                            <div class="px-4 py-3 hover:bg-blue-50 cursor-pointer rounded-xl transition-colors flex items-center justify-between group/item" onclick="selectCustomOption(this, '{{ $sp->id }}', '{{ addslashes($sp->name) }}')">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 group-hover/item:bg-blue-100 group-hover/item:text-blue-500 transition-colors">
                                                        <i class="fas fa-wrench text-xs"></i>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-gray-800 group-hover/item:text-blue-600 transition-colors">{{ $sp->name }}</p>
                                                        <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold mt-0.5">Stok Saat Ini: <span class="text-blue-500">{{ $sp->stock }}</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="new-fields hidden grid grid-cols-2 gap-3">
                            <div class="col-span-2 relative">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Nama Barang Baru</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-blue-400"><i class="fas fa-box-open text-xs"></i></div>
                                    <input type="text" name="items[0][new_name]" placeholder="Misal: Oli Shell Advance" class="w-full pl-9 pr-3 py-3 bg-white border border-blue-200 rounded-xl text-sm font-bold focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none">
                                </div>
                            </div>
                            <div class="relative">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Merek</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-blue-400"><i class="fas fa-tag text-xs"></i></div>
                                    <input type="text" name="items[0][new_brand]" placeholder="Shell" class="w-full pl-9 pr-3 py-3 bg-white border border-blue-200 rounded-xl text-sm font-bold focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none">
                                </div>
                            </div>
                            <div class="relative">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Harga Jual Nanti</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-3 text-blue-400 text-xs font-bold">Rp</span>
                                    <input type="text" name="items[0][selling_price]" placeholder="125.000" oninput="formatVisual(this)" class="w-full pl-9 pr-3 py-3 bg-white border border-blue-200 rounded-xl text-sm font-bold focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none text-right">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="w-full lg:flex-1 grid grid-cols-3 gap-4">
                        <div class="col-span-1">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2 text-center">Qty</label>
                            <input type="text" name="items[0][quantity]" value="1" oninput="formatVisual(this)" class="qty-input w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-center font-black text-gray-800 outline-none focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all">
                        </div>
                        <div class="col-span-2 relative">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2 text-right">Harga Modal / Beli</label>
                            <div class="relative">
                                <span class="absolute left-4 top-3.5 text-gray-400 text-sm font-bold">Rp</span>
                                <input type="text" name="items[0][price]" placeholder="0" oninput="formatVisual(this)" class="price-input w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-right font-black text-gray-800 outline-none focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-end pb-1 lg:pl-2">
                        <button type="button" onclick="removeRow(this)" class="w-12 h-12 flex items-center justify-center text-red-300 hover:text-white hover:bg-red-500 rounded-xl transition-all shadow-sm border border-transparent hover:border-red-600">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex flex-col md:flex-row justify-between items-center gap-6 pt-6 border-t border-gray-100">
            <button type="button" onclick="addRow()" class="px-6 py-3.5 bg-gray-900 hover:bg-gray-800 text-white rounded-2xl text-sm font-bold transition flex items-center gap-2 shadow-lg shadow-gray-900/20 transform hover:-translate-y-0.5">
                <i class="fas fa-plus-circle"></i> Tambah Item Lainnya
            </button>

            <div class="flex flex-col sm:flex-row items-center gap-8 w-full sm:w-auto">
                <div class="text-center sm:text-right w-full sm:w-auto">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Estimasi Nota</p>
                    <p class="text-3xl font-black text-gray-900" id="grandTotalDisplay">Rp 0</p>
                </div>
                
                <div class="flex items-center gap-4 w-full sm:w-auto">
                    <a href="{{ route('admin.purchases.index') }}" class="px-6 py-4 rounded-xl text-gray-500 font-bold hover:bg-gray-100 transition-colors text-center flex-1 sm:flex-none">
                        Batal
                    </a>
                    <button type="submit" class="px-10 py-4 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white rounded-2xl font-black shadow-xl shadow-blue-500/30 transition-transform transform hover:-translate-y-1 flex-1 sm:flex-none text-center">
                        <i class="fas fa-save mr-2"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    let rowCount = 1;
    let activeDropdownMenu = null;

    // FUNGSI UNTUK DROPDOWN KUSTOM
    function toggleCustomDropdown(trigger) {
        const menu = trigger.nextElementSibling;
        const arrow = trigger.querySelector('.arrow-icon');
        const row = trigger.closest('.item-row');

        // Tutup dropdown lain yang sedang terbuka
        if (activeDropdownMenu && activeDropdownMenu !== menu) {
            closeAllDropdowns();
        }

        if (menu.classList.contains('hidden')) {
            // Angkat z-index row yang aktif agar menu tidak tertutup row di bawahnya
            document.querySelectorAll('.item-row').forEach(r => r.style.zIndex = '10');
            row.style.zIndex = '50';

            menu.classList.remove('hidden');
            arrow.classList.add('rotate-180', 'text-blue-500');
            activeDropdownMenu = menu;
        } else {
            closeAllDropdowns();
        }
    }

    function closeAllDropdowns() {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.add('hidden');
        });
        document.querySelectorAll('.arrow-icon').forEach(arrow => {
            arrow.classList.remove('rotate-180', 'text-blue-500');
        });
        document.querySelectorAll('.item-row').forEach(r => r.style.zIndex = '10');
        activeDropdownMenu = null;
    }

    // Menutup dropdown jika user mengklik area di luar dropdown
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-dropdown')) {
            closeAllDropdowns();
        }
    });

    function selectCustomOption(optionElement, value, labelText) {
        const dropdownContainer = optionElement.closest('.custom-dropdown');
        const trigger = dropdownContainer.querySelector('.dropdown-trigger');
        const hiddenInput = dropdownContainer.querySelector('input[type="hidden"]');
        const labelDisplay = trigger.querySelector('.selected-label');
        const type = dropdownContainer.getAttribute('data-type');

        // 1. Update Nilai Input Hidden
        hiddenInput.value = value;
        
        // 2. Update Teks Label
        labelDisplay.innerText = labelText;
        if(value !== '') {
            labelDisplay.classList.remove('text-gray-400');
            labelDisplay.classList.add('text-gray-900');
            trigger.classList.add('border-blue-300', 'bg-white');
        } else {
            labelDisplay.classList.add('text-gray-400');
            labelDisplay.classList.remove('text-gray-900');
            trigger.classList.remove('border-blue-300', 'bg-white');
        }

        // 3. Jika Mode berubah, jalankan logika tampilan form
        if (type === 'mode') {
            applyModeStyles(dropdownContainer, value);
        }

        closeAllDropdowns();
    }

    function applyModeStyles(dropdownContainer, mode) {
        const row = dropdownContainer.closest('.item-row');
        const existingFields = row.querySelector('.existing-fields');
        const newFields = row.querySelector('.new-fields');
        const trigger = dropdownContainer.querySelector('.dropdown-trigger');
        const iconLeft = trigger.querySelector('.icon-left');
        const labelDisplay = trigger.querySelector('.selected-label');

        if (mode === 'new') {
            existingFields.classList.add('hidden');
            newFields.classList.remove('hidden');
            
            // Style Warna Hijau
            trigger.classList.remove('bg-blue-50/50', 'border-blue-100');
            trigger.classList.add('bg-green-50/50', 'border-green-200');
            iconLeft.classList.replace('text-blue-500', 'text-green-500');
            labelDisplay.classList.replace('text-blue-700', 'text-green-700');
        } else {
            existingFields.classList.remove('hidden');
            newFields.classList.add('hidden');
            
            // Style Warna Biru
            trigger.classList.remove('bg-green-50/50', 'border-green-200');
            trigger.classList.add('bg-blue-50/50', 'border-blue-100');
            iconLeft.classList.replace('text-green-500', 'text-blue-500');
            labelDisplay.classList.replace('text-green-700', 'text-blue-700');
        }
    }

    // FUNGSI FORMAT UANG
    function formatVisual(input) {
        let value = input.value.replace(/[^0-9]/g, "");
        if (value !== "") {
            input.value = parseInt(value).toLocaleString('id-ID');
        } else {
            input.value = "";
        }
        calculateTotal();
    }

    document.getElementById('purchaseForm').onsubmit = function() {
        document.querySelectorAll('.qty-input, .price-input, input[name*="selling_price"], input[name*="price"]').forEach(input => {
            input.value = input.value.replace(/\./g, "");
        });
    };

    // FUNGSI TAMBAH BARIS
    function addRow() {
        const container = document.getElementById('items_container');
        const template = document.querySelector('.item-row').cloneNode(true);
        
        // Atur z-index standar
        template.style.zIndex = '10';

        // Sesuaikan Input
        template.querySelectorAll('input').forEach(input => {
            input.name = input.name.replace(/\[\d+\]/, `[${rowCount}]`);
            if(input.type === 'hidden') {
                if(input.classList.contains('mode-input')) input.value = 'existing';
                if(input.classList.contains('sparepart-input')) input.value = '';
            } else {
                input.value = input.classList.contains('qty-input') ? '1' : '';
            }
        });
        
        // Reset Visual Custom Dropdown (Mode)
        const modeTrigger = template.querySelector('.custom-dropdown[data-type="mode"] .dropdown-trigger');
        modeTrigger.className = "dropdown-trigger w-full pl-11 pr-10 py-3.5 bg-blue-50/50 border border-blue-100 rounded-xl flex items-center justify-between cursor-pointer focus:ring-4 focus:ring-blue-100 transition-all group";
        template.querySelector('.custom-dropdown[data-type="mode"] .icon-left').className = "absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-blue-500 icon-left";
        template.querySelector('.custom-dropdown[data-type="mode"] .selected-label').className = "text-sm font-black text-blue-700 selected-label";
        template.querySelector('.custom-dropdown[data-type="mode"] .selected-label').innerText = "Barang Lama";

        // Reset Visual Custom Dropdown (Sparepart)
        const sparepartTrigger = template.querySelector('.custom-dropdown[data-type="sparepart"] .dropdown-trigger');
        sparepartTrigger.className = "dropdown-trigger w-full pl-11 pr-10 py-3.5 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-between cursor-pointer hover:bg-white transition-all group";
        const sparepartLabel = template.querySelector('.custom-dropdown[data-type="sparepart"] .selected-label');
        sparepartLabel.className = "text-sm font-bold text-gray-400 selected-label truncate";
        sparepartLabel.innerText = "-- Pilih Suku Cadang --";

        // Tutup menu jika terbuka saat clone
        template.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('hidden'));
        template.querySelectorAll('.arrow-icon').forEach(arrow => arrow.classList.remove('rotate-180', 'text-blue-500'));

        // Reset form visibility
        template.querySelector('.existing-fields').classList.remove('hidden');
        template.querySelector('.new-fields').classList.add('hidden');

        container.appendChild(template);
        rowCount++;
    }

    function removeRow(btn) {
        if(document.querySelectorAll('.item-row').length > 1) {
            btn.closest('.item-row').remove();
            calculateTotal();
        }
    }

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = row.querySelector('.qty-input').value.replace(/\./g, "") || 0;
            const price = row.querySelector('.price-input').value.replace(/\./g, "") || 0;
            total += (parseInt(qty) * parseInt(price));
        });
        document.getElementById('grandTotalDisplay').innerText = 'Rp ' + total.toLocaleString('id-ID');
    }
</script>
@endsection