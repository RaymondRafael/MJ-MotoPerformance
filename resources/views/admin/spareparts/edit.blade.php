@extends('admin.layouts.app')
@section('title', 'Edit Suku Cadang')
@section('header', 'Inventaris & Gudang')

@section('content')
<div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-8 max-w-3xl mx-auto mt-4">
    
    <div class="mb-8 flex items-center justify-between border-b border-gray-100 pb-5">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl mr-4 shadow-inner">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black text-gray-900 tracking-tight">Edit Data Suku Cadang</h2>
                <p class="text-gray-500 text-sm mt-1">Perbarui nama, merek, atau penyesuaian harga jual.</p>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0"><i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i></div>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-red-800">Gagal Menyimpan Perubahan!</h3>
                    <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.spareparts.update', $sparepart->id) }}" method="POST" id="editForm">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            
            <div class="col-span-1 md:col-span-2 relative">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nama Barang <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <input type="text" name="name" required value="{{ old('name', $sparepart->name) }}" placeholder="Contoh: Oli Mesin Shell Advance AX7 10W-40 1L"
                        class="w-full pl-11 pr-4 py-3.5 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none font-bold text-gray-800 shadow-sm">
                </div>
            </div>

            <div class="relative">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Merek <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-tag"></i>
                    </div>
                    <input type="text" name="brand" required value="{{ old('brand', $sparepart->brand) }}" placeholder="Contoh: Shell"
                        class="w-full pl-11 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none font-bold text-gray-800 shadow-sm">
                </div>
            </div>

            <div class="relative">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Harga Jual Saat Ini <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-4 top-3.5 text-blue-500 text-sm font-black">Rp</span>
                    <input type="text" name="price" required value="{{ number_format(old('price', $sparepart->price), 0, ',', '.') }}" oninput="formatVisual(this)"
                        class="w-full pl-12 pr-4 py-3 bg-white border border-blue-200 rounded-xl focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none font-black text-gray-900 shadow-sm text-right">
                </div>
            </div>

        </div>

        <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 flex flex-col md:flex-row gap-6 mb-8 relative overflow-hidden">
            <div class="absolute -right-4 -top-4 text-gray-100 text-6xl transform rotate-12"><i class="fas fa-lock"></i></div>
            
            <div class="flex-1 relative z-10">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Kategori Barang</label>
                <div class="flex items-center gap-2">
                    <i class="fas fa-tags text-gray-400"></i>
                    <span class="font-bold text-gray-600 bg-gray-200 px-3 py-1 rounded-lg text-sm">{{ $sparepart->category }}</span>
                </div>
            </div>

            <div class="flex-1 relative z-10 border-t md:border-t-0 md:border-l border-gray-200 pt-4 md:pt-0 md:pl-6">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Sisa Stok Gudang</label>
                <div class="flex items-center gap-2">
                    <i class="fas fa-cubes text-gray-400"></i>
                    <span class="font-black text-gray-700 text-lg">{{ $sparepart->stock }}</span>
                </div>
                <p class="text-[10px] text-gray-400 italic mt-1">*Stok hanya bisa diperbarui melalui menu Catat Pembelian.</p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-end gap-4 pt-6 border-t border-gray-100">
            <a href="{{ route('admin.spareparts.index') }}" class="w-full sm:w-auto px-6 py-3.5 rounded-xl text-gray-500 font-bold hover:bg-gray-100 transition-colors text-center">
                Batal
            </a>
            <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white rounded-xl font-bold transition-transform transform hover:-translate-y-1 shadow-lg shadow-blue-500/30 text-center flex items-center justify-center gap-2">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script>
    // Script untuk format Rupiah saat admin mengetik
    function formatVisual(input) {
        let value = input.value.replace(/[^0-9]/g, "");
        if (value !== "") {
            input.value = parseInt(value).toLocaleString('id-ID');
        } else {
            input.value = "";
        }
    }

    // Hapus format titik sebelum data dikirim ke Controller
    document.getElementById('editForm').onsubmit = function() {
        const priceInput = document.querySelector('input[name="price"]');
        priceInput.value = priceInput.value.replace(/\./g, "");
    };
</script>
@endsection