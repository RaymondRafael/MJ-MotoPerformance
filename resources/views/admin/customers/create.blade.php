@extends('admin.layouts.app')
@section('title', 'Tambah Pelanggan Baru')
@section('header', 'Manajemen Pelanggan')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.customers.index') }}" class="text-gray-500 hover:text-gray-700 transition flex items-center gap-2 font-medium">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Pelanggan
        </a>
    </div>

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b bg-gray-50">
            <h2 class="font-bold text-gray-800 text-lg">Form Tambah Pelanggan & Akun Login</h2>
            <p class="text-gray-500 text-sm mt-1">Isi formulir di bawah ini untuk mendaftarkan profil pelanggan sekaligus membuatkan mereka akun untuk masuk ke aplikasi mobile.</p>
        </div>

        <form action="{{ route('admin.customers.store') }}" method="POST" class="p-6">
            @csrf <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="name" class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Budi Santoso"
                        class="w-full px-4 py-2 border @error('name') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-red-500 focus:border-red-500 transition">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone_number" class="block text-sm font-bold text-gray-700 mb-2">Nomor WhatsApp <span class="text-red-500">*</span></label>
                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" required placeholder="Contoh: 08123456789"
                        class="w-full px-4 py-2 border @error('phone_number') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-red-500 focus:border-red-500 transition">
                    @error('phone_number')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="email" class="block text-sm font-bold text-gray-700 mb-2">Email Login Aplikasi <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="budi@contoh.com"
                        class="w-full px-4 py-2 border @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-red-500 focus:border-red-500 transition">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-bold text-gray-700 mb-2">Password Awal <span class="text-red-500">*</span></label>
                    <input type="password" name="password" id="password" required placeholder="Minimal 6 Karakter"
                        class="w-full px-4 py-2 border @error('password') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-red-500 focus:border-red-500 transition">
                    <p class="text-gray-400 text-xs mt-1">Berikan password ini kepada pelanggan untuk login.</p>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-8">
                <label for="address" class="block text-sm font-bold text-gray-700 mb-2">Alamat Lengkap <span class="text-red-500">*</span></label>
                <textarea name="address" id="address" rows="3" required placeholder="Masukkan alamat lengkap rumah/kantor pelanggan..."
                    class="w-full px-4 py-2 border @error('address') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-red-500 focus:border-red-500 transition">{{ old('address') }}</textarea>
                @error('address')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-4 border-t pt-6">
                <a href="{{ route('admin.customers.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-bold rounded-lg transition flex items-center gap-2">
                    <i class="fas fa-save"></i> Simpan Pelanggan Baru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection