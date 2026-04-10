@extends('admin.layouts.app')
@section('title', 'Edit Mekanik')
@section('header', 'Manajemen Mekanik')

@section('content')
<div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-8 max-w-3xl mx-auto mt-4">
    
    <div class="mb-8 flex items-center justify-between border-b border-gray-100 pb-5">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-red-50 text-red-600 rounded-xl flex items-center justify-center text-xl mr-4 shadow-inner">
                <i class="fas fa-user-cog"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black text-gray-900 tracking-tight">Edit Data Mekanik</h2>
                <p class="text-gray-500 text-sm mt-1">Perbarui informasi profil dan kontak mekanik bengkel.</p>
            </div>
        </div>
        <a href="{{ route('admin.mechanics.index') }}" class="hidden sm:flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 hover:text-red-600 transition-all font-bold shadow-sm">
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

    <form action="{{ route('admin.mechanics.update', $mechanic->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="relative z-20">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nama Mekanik <span class="text-red-500">*</span></label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                    <i class="fas fa-user-tie"></i>
                </div>
                <input type="text" name="name" value="{{ old('name', $mechanic->name) }}" required placeholder="Contoh: Budi Santoso"
                    class="w-full pl-11 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-red-50 focus:border-red-500 transition-all outline-none font-bold text-gray-800 capitalize">
            </div>
        </div>

        <div class="relative z-10">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nomor Handphone / WhatsApp</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                    <i class="fas fa-phone-alt"></i>
                </div>
                <input type="text" name="phone_number" value="{{ old('phone_number', $mechanic->phone_number) }}" placeholder="Contoh: 08123456789"
                    class="w-full pl-11 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-red-50 focus:border-red-500 transition-all outline-none font-bold text-gray-800">
            </div>
        </div>

        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100 mt-10">
            <a href="{{ route('admin.mechanics.index') }}" class="flex items-center gap-2 px-6 py-3.5 rounded-xl text-gray-500 font-bold hover:bg-gray-100 transition-colors">
                <i class="fas fa-times"></i> Batal
            </a>
            <button type="submit" class="flex items-center gap-2 px-8 py-3.5 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-500 hover:to-red-400 text-white rounded-xl font-bold transition-transform transform hover:-translate-y-1 shadow-lg shadow-red-500/30">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection