@extends('admin.layouts.app')
@section('title', 'Riwayat Pembelian')
@section('header', 'Manajemen Inventaris')

@section('content')
<div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden mt-4">
    
    <div class="p-6 md:p-8 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl shadow-inner shrink-0">
                <i class="fas fa-history"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black text-gray-900 tracking-tight">Riwayat Pembelian</h2>
                <p class="text-gray-500 text-sm mt-1">Daftar riwayat barang masuk dari supplier.</p>
            </div>
        </div>
        
        <a href="{{ route('admin.purchases.create') }}" class="w-full md:w-auto bg-gray-900 hover:bg-gray-800 text-white px-6 py-3.5 rounded-xl text-sm font-bold transition flex items-center justify-center gap-2 shadow-lg shadow-gray-900/20 transform hover:-translate-y-0.5">
            <i class="fas fa-plus-circle"></i> Catat Pembelian
        </a>
    </div>

    <div class="p-6 bg-gray-50 border-b border-gray-100">
        <form action="{{ route('admin.purchases.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            
            <div class="flex-1 w-full">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Cari Supplier</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-search"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama toko / distributor..." 
                        class="w-full pl-11 pr-4 py-3.5 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none font-bold text-gray-800 shadow-sm text-sm">
                </div>
            </div>

            <div class="flex gap-4 w-full md:w-auto">
                <div class="flex-1 md:w-40 relative">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" 
                        class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none font-bold text-gray-800 shadow-sm text-sm">
                </div>
                <div class="flex-1 md:w-40 relative">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" 
                        class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none font-bold text-gray-800 shadow-sm text-sm">
                </div>
            </div>

            <div class="flex gap-3 w-full md:w-auto">
                <button type="submit" class="flex-1 md:flex-none px-8 py-3.5 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white rounded-xl text-sm font-bold transition shadow-lg shadow-blue-500/30 flex items-center justify-center gap-2">
                    <i class="fas fa-filter"></i> Terapkan
                </button>
                
                @if(request()->hasAny(['search', 'start_date', 'end_date']))
                    <a href="{{ route('admin.purchases.index') }}" title="Reset Filter" class="px-5 py-3.5 bg-white border border-gray-200 text-gray-500 hover:text-red-600 hover:bg-red-50 hover:border-red-200 rounded-xl text-sm font-bold transition flex items-center justify-center shadow-sm">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-xs uppercase text-gray-400 border-b border-gray-100 bg-white">
                    <th class="p-5 font-bold tracking-widest">Tanggal</th>
                    <th class="p-5 font-bold tracking-widest">Supplier</th>
                    <th class="p-5 font-bold tracking-widest text-right">Total Biaya</th>
                    <th class="p-5 font-bold tracking-widest text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 divide-y divide-gray-50">
                @forelse($purchases as $p)
                <tr class="hover:bg-blue-50/30 transition-colors group">
                    <td class="p-5 font-bold text-gray-500">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 group-hover:bg-blue-100 group-hover:text-blue-500 transition-colors">
                                <i class="fas fa-calendar-day text-xs"></i>
                            </div>
                            {{ \Carbon\Carbon::parse($p->purchase_date)->format('d M Y') }}
                        </div>
                    </td>
                    <td class="p-5 font-black text-gray-800">{{ $p->supplier_name }}</td>
                    <td class="p-5 text-right font-black text-gray-900">
                        <span class="text-xs text-gray-400 font-bold mr-1">Rp</span>{{ number_format($p->total_cost, 0, ',', '.') }}
                    </td>
                    <td class="p-5 text-center">
                        <a href="{{ route('admin.purchases.show', $p->id) }}" class="inline-flex items-center justify-center bg-gray-100 hover:bg-blue-600 text-gray-600 hover:text-white px-4 py-2 rounded-lg transition-colors font-bold text-xs gap-2">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-12 text-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3 text-gray-300">
                            <i class="fas fa-inbox text-2xl"></i>
                        </div>
                        <p class="text-gray-400 font-bold">Belum ada data pembelian masuk.</p>
                        @if(request()->hasAny(['search', 'start_date', 'end_date']))
                            <p class="text-gray-400 text-xs mt-1">Coba sesuaikan kata kunci atau rentang tanggal filter Anda.</p>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if(method_exists($purchases, 'links'))
        <div class="p-5 border-t border-gray-100 bg-gray-50">
            {{ $purchases->appends(request()->query())->links() }}
        </div>
    @endif
    
</div>
@endsection