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

<div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-8 max-w-5xl mx-auto mt-4">
    
    <div class="mb-8 flex items-center justify-between border-b border-gray-100 pb-5">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl mr-4 shadow-inner">
                <i class="fas fa-boxes"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black text-gray-900 tracking-tight">Penerimaan Barang & Stok</h2>
                <p class="text-gray-500 text-sm mt-1">Gunakan Kode Barang (SKU) unik untuk mempermudah pelacakan aset gudang.</p>
            </div>
        </div>
        <a href="{{ route('admin.purchases.index') }}" class="hidden sm:flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 hover:text-blue-600 transition-all font-bold shadow-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="{{ route('admin.purchases.store') }}" method="POST" id="purchaseForm">
        @if (session('error'))
            <div class="mb-8 p-5 bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-sm flex items-start gap-4">
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
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Mode Input</label>
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
                        
                        <div class="existing-fields space-y-3">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Pilih Suku Cadang</label>
                                <div class="relative custom-dropdown" data-type="sparepart">
                                    <input type="hidden" name="items[0][sparepart_id]" class="sparepart-input" value="">

                                    <div class="dropdown-trigger w-full pl-11 pr-10 py-3.5 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-between cursor-pointer hover:bg-white transition-all group" onclick="toggleCustomDropdown(this, true)">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                            <i class="fas fa-cogs"></i>
                                        </div>
                                        <span class="text-sm font-bold text-gray-400 selected-label truncate">-- Pilih Suku Cadang --</span>
                                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400 transition-transform duration-300 arrow-icon">
                                            <i class="fas fa-chevron-down text-xs"></i>
                                        </div>
                                    </div>

                                    <div class="dropdown-menu hidden absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden">
                                        <div class="sticky top-0 bg-white border-b border-gray-100 p-3 z-10">
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                                    <i class="fas fa-search text-xs"></i>
                                                </div>
                                                <input type="text" class="search-input w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-gray-700 focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all" placeholder="Cari Kode, Nama, atau Kategori..." autocomplete="off" oninput="filterDropdownList(this)" onclick="event.stopPropagation()">
                                            </div>
                                        </div>

                                        <div class="dropdown-list-container max-h-60 overflow-y-auto custom-scrollbar p-2 space-y-1">
                                            <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer rounded-xl transition-colors font-bold text-gray-400 flex items-center gap-3" onclick="selectCustomOption(this, '', '-- Pilih Suku Cadang --', '')">
                                                -- Batal Memilih --
                                            </div>
                                            @foreach($spareparts as $sp)
                                                <div class="dropdown-item px-4 py-3 hover:bg-blue-50 cursor-pointer rounded-xl transition-colors flex items-center justify-between group/item" onclick="selectCustomOption(this, '{{ $sp->id }}', '[{{ strtoupper($sp->code) }}] {{ addslashes($sp->name) }}', '{{ $sp->price }}')">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 group-hover/item:bg-blue-100 group-hover/item:text-blue-500 transition-colors">
                                                            <i class="fas fa-wrench text-xs"></i>
                                                        </div>
                                                        <div>
                                                            <div class="flex items-center gap-2 mb-0.5">
                                                                <p class="font-bold text-gray-800 group-hover/item:text-blue-600 transition-colors item-name">
                                                                    <span class="text-blue-600 font-mono text-xs mr-1 uppercase">[{{ strtoupper($sp->code) }}]</span>{{ $sp->name }}
                                                                </p>
                                                                <span class="item-category text-[9px] uppercase tracking-widest bg-gray-200 text-gray-600 px-1.5 py-0.5 rounded font-bold">{{ $sp->category->name ?? 'Lainnya' }}</span>
                                                            </div>
                                                            @if($sp->stock <= 5)
                                                                <p class="text-[10px] text-red-500 uppercase tracking-widest font-black mt-0.5"><i class="fas fa-exclamation-triangle mr-1"></i> Stok Menipis: {{ $sp->stock }}</p>
                                                            @else
                                                                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold mt-0.5">Stok Saat Ini: <span class="text-blue-500">{{ $sp->stock }}</span></p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            
                                            <div class="no-item-found hidden px-5 py-6 text-center">
                                                <i class="fas fa-search-minus text-gray-300 text-2xl mb-2"></i>
                                                <p class="text-sm font-bold text-gray-500">Barang tidak ditemukan.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="relative info-harga-container hidden">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Harga Jual Di Sistem (Info)</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-3.5 text-blue-400 text-xs font-bold">Rp</span>
                                    <input type="text" class="info-harga-input w-full pl-9 pr-3 py-3 bg-gray-100 border border-gray-200 rounded-xl text-sm font-bold text-gray-500 outline-none cursor-not-allowed text-right" readonly placeholder="0">
                                </div>
                            </div>
                        </div>

                        <div class="new-fields hidden grid grid-cols-2 gap-3">
                            <div class="col-span-2 relative">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Kode Barang (SKU) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-blue-400">
                                        <i class="fas fa-barcode text-xs"></i>
                                    </div>
                                    <input type="text" name="items[0][new_code]" placeholder="Contoh: OLI-MPX2-01" class="sku-input w-full pl-9 pr-3 py-3 bg-white border border-blue-200 rounded-xl text-sm font-bold focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none uppercase">
                                </div>
                            </div>

                            <div class="col-span-2 relative">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Nama Barang Baru <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-blue-400"><i class="fas fa-box-open text-xs"></i></div>
                                    <input type="text" name="items[0][new_name]" placeholder="Misal: Oli Shell Advance" class="w-full pl-9 pr-3 py-3 bg-white border border-blue-200 rounded-xl text-sm font-bold focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none">
                                </div>
                            </div>

                            <div class="col-span-2 relative">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Merek <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-blue-400"><i class="fas fa-tag text-xs"></i></div>
                                    <input type="text" name="items[0][brand]" placeholder="Shell / Honda" class="w-full pl-9 pr-3 py-3 bg-white border border-blue-200 rounded-xl text-sm font-bold focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none">
                                </div>
                            </div>
                            
                            <!-- DROPDOWN KATEGORI BARU DENGAN SEARCH & DEFAULT KOSONG -->
                            <div class="col-span-2 relative">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Pilih Kategori</label>
                                <div class="relative custom-dropdown" data-type="category">
                                    <input type="hidden" name="items[0][new_category]" class="category-input" value="">
                                    
                                    <div class="dropdown-trigger w-full pl-11 pr-10 py-3 bg-white border border-blue-200 rounded-xl flex items-center justify-between cursor-pointer focus:ring-4 focus:ring-blue-50 transition-all group" onclick="toggleCustomDropdown(this, true)">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-blue-500">
                                            <i class="fas fa-tags text-xs"></i>
                                        </div>
                                        <span class="text-sm font-bold text-gray-400 selected-label truncate">-- Belum Terpilih --</span>
                                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-blue-400 transition-transform duration-300 arrow-icon">
                                            <i class="fas fa-chevron-down text-xs"></i>
                                        </div>
                                    </div>

                                    <div class="dropdown-menu hidden absolute z-50 w-full mt-1 bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden">
                                        <div class="sticky top-0 bg-white border-b border-gray-100 p-2 z-10">
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                                    <i class="fas fa-search text-[10px]"></i>
                                                </div>
                                                <input type="text" class="search-input w-full pl-8 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs font-bold text-gray-700 focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all" placeholder="Cari kategori..." autocomplete="off" oninput="filterDropdownList(this)" onclick="event.stopPropagation()">
                                            </div>
                                        </div>

                                        <div class="dropdown-list-container max-h-48 overflow-y-auto custom-scrollbar p-2 space-y-1">
                                            <div class="dropdown-item px-4 py-2 hover:bg-gray-50 cursor-pointer rounded-lg transition-colors font-bold text-gray-400 text-xs flex items-center gap-3" onclick="selectCustomOption(this, '', '-- Belum Terpilih --')">
                                                -- Batal Memilih --
                                            </div>
                                            @foreach($categories as $cat)
                                                <div class="dropdown-item px-4 py-2 hover:bg-blue-50 cursor-pointer rounded-lg transition-colors font-bold text-gray-800 text-xs flex items-center gap-3" onclick="selectCustomOption(this, '{{ $cat }}', '{{ $cat }}')">
                                                    <span class="item-name">{{ $cat }}</span>
                                                </div>
                                            @endforeach
                                            <div class="no-item-found hidden px-4 py-4 text-center">
                                                <p class="text-xs font-bold text-gray-500">Kategori tidak ditemukan.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-span-2 relative">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Atau Tulis Kategori Baru <span class="text-gray-400 font-normal">(Kosongkan jika menggunakan pilihan di atas)</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <i class="fas fa-folder-plus text-xs"></i>
                                    </div>
                                    <input type="text" name="items[0][custom_category]" placeholder="Misal: Kelistrikan / Aksesoris" class="w-full pl-9 pr-3 py-3 bg-white border border-blue-200 rounded-xl text-sm font-bold focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none">
                                </div>
                            </div>

                            <div class="col-span-2 relative">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Harga Jual Nanti <span class="text-red-500">*</span></label>
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

    function toggleCustomDropdown(trigger, focusSearch = false) {
        const menu = trigger.nextElementSibling;
        const arrow = trigger.querySelector('.arrow-icon');
        const row = trigger.closest('.item-row');

        if (activeDropdownMenu && activeDropdownMenu !== menu) {
            closeAllDropdowns();
        }

        if (menu.classList.contains('hidden')) {
            document.querySelectorAll('.item-row').forEach(r => r.style.zIndex = '10');
            row.style.zIndex = '50';

            menu.classList.remove('hidden');
            if(arrow) arrow.classList.add('rotate-180', 'text-blue-500');
            activeDropdownMenu = menu;

            if (focusSearch) {
                const searchInput = menu.querySelector('.search-input');
                if (searchInput) {
                    setTimeout(() => searchInput.focus(), 50);
                }
            }
        } else {
            closeAllDropdowns();
        }
    }

    function closeAllDropdowns() {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.add('hidden');
            const searchInput = menu.querySelector('.search-input');
            if(searchInput) {
                searchInput.value = '';
                filterDropdownList(searchInput);
            }
        });
        document.querySelectorAll('.arrow-icon').forEach(arrow => {
            arrow.arrow = arrow.classList.remove('rotate-180', 'text-blue-500');
        });
        document.querySelectorAll('.item-row').forEach(r => r.style.zIndex = '10');
        activeDropdownMenu = null;
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-dropdown')) {
            closeAllDropdowns();
        }
    });

    function selectCustomOption(optionElement, value, labelText, sellingPrice = '') {
        const dropdownContainer = optionElement.closest('.custom-dropdown');
        const trigger = dropdownContainer.querySelector('.dropdown-trigger');
        const hiddenInput = dropdownContainer.querySelector('input[type="hidden"]');
        const labelDisplay = trigger.querySelector('.selected-label');
        const type = dropdownContainer.getAttribute('data-type');

        hiddenInput.value = value;
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

        if (type === 'mode') {
            applyModeStyles(dropdownContainer, value);
        }

        if (type === 'sparepart') {
            const row = dropdownContainer.closest('.item-row');
            const infoContainer = row.querySelector('.info-harga-container');
            const infoInput = row.querySelector('.info-harga-input');
            
            if (value !== '' && sellingPrice !== '') {
                infoContainer.classList.remove('hidden');
                infoInput.value = parseInt(sellingPrice).toLocaleString('id-ID');
                
                infoInput.classList.remove('bg-gray-100', 'text-gray-500');
                infoInput.classList.add('bg-blue-50', 'text-blue-600');
                setTimeout(() => {
                    infoInput.classList.add('bg-gray-100', 'text-gray-500');
                    infoInput.classList.remove('bg-blue-50', 'text-blue-600');
                }, 500);
            } else {
                infoContainer.classList.add('hidden');
                infoInput.value = '';
            }
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
            trigger.classList.remove('bg-blue-50/50', 'border-blue-100');
            trigger.classList.add('bg-green-50/50', 'border-green-200');
            iconLeft.classList.replace('text-blue-500', 'text-green-500');
            labelDisplay.classList.replace('text-blue-700', 'text-green-700');
        } else {
            existingFields.classList.remove('hidden');
            newFields.classList.add('hidden');
            trigger.classList.remove('bg-green-50/50', 'border-green-200');
            trigger.classList.add('bg-blue-50/50', 'border-blue-100');
            iconLeft.classList.replace('text-green-500', 'text-blue-500');
            labelDisplay.classList.replace('text-green-700', 'text-blue-700');
        }
    }

    // ALGORITMA LIVE SEARCH: MENCAKUP PENCARIAN KODE BARANG, NAMA, DAN KATEGORI
    function filterDropdownList(inputElement) {
        const query = inputElement.value.toLowerCase();
        const container = inputElement.closest('.dropdown-menu').querySelector('.dropdown-list-container');
        if (!container) return;
        const items = container.querySelectorAll('.dropdown-item');
        let hasVisibleItem = false;

        items.forEach(item => {
            const nameElement = item.querySelector('.item-name');
            const categoryElement = item.querySelector('.item-category');

            if (nameElement) {
                const fullText = nameElement.innerText.toLowerCase();
                const category = categoryElement ? categoryElement.innerText.toLowerCase() : '';

                if (fullText.includes(query) || category.includes(query)) {
                    item.classList.remove('hidden');
                    item.classList.add('flex');
                    hasVisibleItem = true;
                } else {
                    item.classList.remove('flex');
                    item.classList.add('hidden');
                }
            }
        });

        const noFoundMsg = container.querySelector('.no-item-found');
        if (noFoundMsg) {
            if (!hasVisibleItem && query !== '') {
                noFoundMsg.classList.remove('hidden');
            } else {
                noFoundMsg.classList.add('hidden');
            }
        }
    }

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

    function addRow() {
        const container = document.getElementById('items_container');
        const template = document.querySelector('.item-row').cloneNode(true);
        
        template.style.zIndex = '10';

        template.querySelectorAll('input, select').forEach(input => {
            input.name = input.name.replace(/\[\d+\]/, `[${rowCount}]`);
            
            if(input.type === 'hidden') {
                if(input.classList.contains('mode-input')) input.value = 'existing';
                if(input.classList.contains('sparepart-input')) input.value = '';
                
                // Kategori kini di-reset menjadi kosong (Belum Terpilih)
                if(input.classList.contains('category-input')) input.value = '';
            } else {
                if(input.classList.contains('search-input')) {
                    input.value = ''; 
                } else if(!input.classList.contains('info-harga-input')) {
                    input.value = input.classList.contains('qty-input') ? '1' : '';
                }
                
                if(input.name.includes('custom_category')) {
                    input.value = '';
                }
            }
        });

        template.querySelectorAll('.dropdown-item').forEach(item => {
            item.classList.remove('hidden');
            item.classList.add('flex');
        });
        const noFoundMsg = template.querySelector('.no-item-found');
        if(noFoundMsg) noFoundMsg.classList.add('hidden');
        
        const modeTrigger = template.querySelector('.custom-dropdown[data-type="mode"] .dropdown-trigger');
        modeTrigger.className = "dropdown-trigger w-full pl-11 pr-10 py-3.5 bg-blue-50/50 border border-blue-100 rounded-xl flex items-center justify-between cursor-pointer focus:ring-4 focus:ring-blue-100 transition-all group";
        template.querySelector('.custom-dropdown[data-type="mode"] .icon-left').className = "absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-blue-500 icon-left";
        template.querySelector('.custom-dropdown[data-type="mode"] .selected-label').className = "text-sm font-black text-blue-700 selected-label";
        template.querySelector('.custom-dropdown[data-type="mode"] .selected-label').innerText = "Barang Lama";

        const sparepartTrigger = template.querySelector('.custom-dropdown[data-type="sparepart"] .dropdown-trigger');
        sparepartTrigger.className = "dropdown-trigger w-full pl-11 pr-10 py-3.5 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-between cursor-pointer hover:bg-white transition-all group";
        const sparepartLabel = template.querySelector('.custom-dropdown[data-type="sparepart"] .selected-label');
        sparepartLabel.className = "text-sm font-bold text-gray-400 selected-label truncate";
        sparepartLabel.innerText = "-- Pilih Suku Cadang --";

        // RESET DROPDOWN KATEGORI SAAT KLONING BARIS BARU
        const categoryTrigger = template.querySelector('.custom-dropdown[data-type="category"] .dropdown-trigger');
        if (categoryTrigger) {
            categoryTrigger.className = "dropdown-trigger w-full pl-11 pr-10 py-3 bg-white border border-blue-200 rounded-xl flex items-center justify-between cursor-pointer focus:ring-4 focus:ring-blue-50 transition-all group";
            const categoryLabel = template.querySelector('.custom-dropdown[data-type="category"] .selected-label');
            categoryLabel.className = "text-sm font-bold text-gray-400 selected-label truncate";
            categoryLabel.innerText = "-- Belum Terpilih --"; 
        }

        template.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('hidden'));
        template.querySelectorAll('.arrow-icon').forEach(arrow => arrow.classList.remove('rotate-180', 'text-blue-500'));

        template.querySelector('.existing-fields').classList.remove('hidden');
        template.querySelector('.new-fields').classList.add('hidden');

        const infoContainer = template.querySelector('.info-harga-container');
        if(infoContainer) {
            infoContainer.classList.add('hidden');
            template.querySelector('.info-harga-input').value = '';
        }

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