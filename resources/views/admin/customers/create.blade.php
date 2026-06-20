@extends('admin.layouts.app')
@section('title', 'Tambah Pelanggan Baru')
@section('header', 'Manajemen Pelanggan')

@section('content')
<div class="max-w-4xl mx-auto mt-4">
    
    <div class="mb-8 flex items-center justify-between border-b border-gray-100 pb-5">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl mr-4 shadow-inner">
                <i class="fas fa-user-plus"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black text-gray-900 tracking-tight">Tambah Pelanggan & Akun</h2>
                <p class="text-gray-500 text-sm mt-1">Daftarkan profil pelanggan sekaligus buatkan akses aplikasi mereka.</p>
            </div>
        </div>
        <a href="{{ route('admin.customers.index') }}" class="hidden sm:flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 hover:text-blue-600 transition-all font-bold shadow-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="mb-6 p-5 bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-sm flex items-start gap-4 animate-fade-in-up">
            <div class="mt-0.5 text-red-500 text-xl"><i class="fas fa-exclamation-circle"></i></div>
            <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden">
        <form action="{{ route('admin.customers.store') }}" method="POST" class="p-8 space-y-8">
            @csrf 

            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i class="fas fa-id-card text-blue-500"></i> Informasi Profil
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="relative">
                        <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                        <div class="absolute top-[34px] left-4 text-gray-400"><i class="fas fa-user"></i></div>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Budi Santoso"
                            class="w-full pl-11 pr-4 py-4 bg-gray-50 border @error('name') border-red-500 @else border-gray-200 @enderror rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none font-bold text-gray-800">
                        @error('name') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1 uppercase">{{ $message }}</p> @enderror
                    </div>

                    <div class="relative">
                        <label for="phone_number" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nomor WhatsApp <span class="text-red-500">*</span></label>
                        <div class="absolute top-[34px] left-4 text-gray-400"><i class="fab fa-whatsapp"></i></div>
                        <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" required placeholder="0812xxxx"
                            class="w-full pl-11 pr-4 py-4 bg-gray-50 border @error('phone_number') border-red-500 @else border-gray-200 @enderror rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none font-bold text-gray-800">
                        @error('phone_number') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1 uppercase">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i class="fas fa-key text-blue-500"></i> Akun Aplikasi
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="relative">
                        <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Email Akun <span class="text-red-500">*</span></label>
                        <div class="absolute top-[34px] left-4 text-gray-400"><i class="fas fa-envelope"></i></div>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="email@gmail.com"
                            class="w-full pl-11 pr-4 py-4 bg-gray-50 border @error('email') border-red-500 @else border-gray-200 @enderror rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none font-bold text-gray-800">
                        @error('email') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1 uppercase">{{ $message }}</p> @enderror
                    </div>

                    <div class="relative">
                        <label for="password" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Password Awal <span class="text-red-500">*</span></label>
                        <div class="absolute top-[34px] left-4 text-gray-400"><i class="fas fa-lock"></i></div>
                        <input type="password" name="password" id="password" required placeholder="Minimal 6 Karakter"
                            class="w-full pl-11 pr-12 py-4 bg-gray-50 border @error('password') border-red-500 @else border-gray-200 @enderror rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none font-bold text-gray-800">
                        
                        <button type="button" onclick="togglePassword()" class="absolute top-[34px] right-4 h-11 w-10 flex items-center justify-center text-gray-400 hover:text-blue-500 transition">
                            <i id="eye-icon" class="fas fa-eye"></i>
                        </button>

                        @error('password') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1 uppercase">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="relative">
                <label for="address" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Alamat Domisili <span class="text-red-500">*</span></label>
                <div class="absolute top-12 left-4 text-gray-400"><i class="fas fa-map-marker-alt"></i></div>
                <textarea name="address" id="address" rows="3" required placeholder="Masukkan alamat lengkap pelanggan..."
                    class="w-full pl-11 pr-4 py-4 bg-gray-50 border @error('address') border-red-500 @else border-gray-200 @enderror rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none font-bold text-gray-800 resize-none">{{ old('address') }}</textarea>
                @error('address') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1 uppercase">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end gap-4 border-t border-gray-100 pt-8">
                <a href="{{ route('admin.customers.index') }}" class="px-6 py-3.5 text-gray-500 font-bold hover:bg-gray-100 rounded-2xl transition-colors">
                    Batal
                </a>
                <button type="submit" class="flex items-center gap-2 px-10 py-3.5 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white rounded-2xl font-black shadow-xl shadow-blue-500/20 transition-transform transform hover:-translate-y-1">
                    <i class="fas fa-save"></i> Simpan Pelanggan Baru
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
</script>

@endsection