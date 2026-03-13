@extends('admin.layouts.app')
@section('title', 'Data Suku Cadang')
@section('header', 'Inventaris Suku Cadang')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b flex flex-col md:flex-row justify-between items-center bg-gray-50 gap-4">
        <h2 class="font-bold text-gray-700">Daftar Stok Suku Cadang</h2>
        
        <div class="flex items-center gap-3 w-full md:w-auto">
            <form action="{{ route('admin.spareparts.index') }}" method="GET" class="flex w-full md:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama barang..." 
                    class="px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-red-500 focus:border-red-500 text-sm w-full md:w-64">
                <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-r-lg border border-l-0 border-gray-300 transition">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            <a href="{{ route('admin.spareparts.create') }}" class="bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-bold transition whitespace-nowrap">
                <i class="fas fa-plus mr-1"></i> Tambah
            </a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white border-b border-gray-100 text-xs uppercase text-gray-500">
                    <th class="p-4 font-bold">Nama Suku Cadang</th>
                    <th class="p-4 font-bold">Harga Jual</th>
                    <th class="p-4 font-bold">Stok Tersedia</th>
                    <th class="p-4 font-bold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                @foreach($spareparts as $sparepart)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4 font-bold text-gray-900">{{ $sparepart->name }}</td>
                    <td class="p-4 text-red-600 font-bold">Rp {{ number_format($sparepart->price, 0, ',', '.') }}</td>
                    <td class="p-4">
                        <span class="px-2 py-1 rounded text-xs font-bold {{ $sparepart->stock > 5 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $sparepart->stock }} Pcs
                        </span>
                    </td>
                    <td class="p-4 text-center flex justify-center space-x-2">
                        <a href="{{ route('admin.spareparts.edit', $sparepart->id) }}" class="text-blue-500 hover:text-blue-700 p-2 bg-blue-50 rounded-lg"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.spareparts.destroy', $sparepart->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus suku cadang ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 p-2 bg-red-50 rounded-lg"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection