@extends('admin.layouts.app')
@section('title', 'Tambah Mekanik')
@section('header', 'Manajemen Mekanik')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 max-w-3xl mx-auto">
    <div class="mb-6 border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800"><i class="fas fa-wrench text-red-500 mr-2"></i>Form Tambah Mekanik</h2>
        <p class="text-sm text-gray-500">Silakan isi identitas mekanik baru di bawah ini.</p>
    </div>

    <form action="{{ route('admin.mechanics.store') }}" method="POST" class="space-y-6">
        @csrf
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap Mekanik <span class="text-red-500">*</span></label>
            <input type="text" name="name" required placeholder="Contoh: Kang Asep"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-red-500 focus:border-red-500 transition">
        </div>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Nomor HP / WhatsApp</label>
            <input type="text" name="phone_number" placeholder="Contoh: 08123456789"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-red-500 focus:border-red-500 transition">
        </div>

        <div class="flex justify-end gap-4 pt-4 border-t mt-8">
            <a href="{{ route('admin.mechanics.index') }}" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 font-bold hover:bg-gray-50 transition">Batal</a>
            <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition shadow-lg shadow-red-500/30">Simpan Data</button>
        </div>
    </form>
</div>
@endsection