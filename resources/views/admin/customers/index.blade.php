@extends('admin.layouts.app')
@section('title', 'Data Pelanggan')
@section('header', 'Manajemen Pelanggan')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    
    <div class="p-6 border-b flex flex-col md:flex-row justify-between items-center bg-gray-50 gap-4">
        <h2 class="font-bold text-gray-700">Daftar Pelanggan Terdaftar</h2>
        
        <div class="flex items-center gap-3 w-full md:w-auto">
            <form action="{{ route('admin.customers.index') }}" method="GET" class="flex w-full md:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama atau WA..." 
                    class="px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-red-500 focus:border-red-500 text-sm w-full md:w-64">
                <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-r-lg border border-l-0 border-gray-300 transition">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            <a href="{{ route('admin.customers.create') }}" class="bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-bold transition whitespace-nowrap">
                <i class="fas fa-plus mr-1"></i> Tambah
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="m-6 mb-0 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="m-6 mb-0 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded">
        {{ session('error') }}
    </div>
    @endif

    <div class="overflow-x-auto mt-6">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white border-b border-gray-100 text-xs uppercase text-gray-500">
                    <th class="p-4 font-bold">Nama Pelanggan</th>
                    <th class="p-4 font-bold">No. WhatsApp</th>
                    <th class="p-4 font-bold">Alamat</th>
                    <th class="p-4 font-bold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                @forelse($customers as $customer)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4 font-bold text-gray-900">{{ $customer->name }}</td>
                    <td class="p-4"><i class="fab fa-whatsapp text-green-500 mr-1"></i> {{ $customer->phone_number }}</td>
                    <td class="p-4">{{ $customer->address ?? '-' }}</td>
                    <td class="p-4 text-center flex justify-center space-x-2">
                        <a href="{{ route('admin.customers.edit', $customer->id) }}" class="text-blue-500 hover:text-blue-700 p-2 bg-blue-50 rounded-lg"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus pelanggan ini beserta data kendaraannya?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 p-2 bg-red-50 rounded-lg"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-8 text-center text-gray-400">Tidak ada data pelanggan yang ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection