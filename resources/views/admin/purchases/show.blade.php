@extends('admin.layouts.app')
@section('title', 'Detail Pembelian')
@section('header', 'Inventaris & Pembelian')

@section('content')
<div class="max-w-5xl mx-auto mt-4">
    
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('admin.purchases.index') }}" class="text-gray-600 hover:text-red-600 font-bold transition flex items-center gap-2 px-5 py-2.5 bg-white rounded-xl border border-gray-200 shadow-sm w-max hover:bg-gray-50">
            <i class="fas fa-arrow-left"></i> Kembali ke Riwayat
        </a>

        @if (session('success'))
            <div class="px-4 py-2 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm font-bold shadow-sm animate-fade-in-down">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            </div>
        @endif
    </div>

    <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden relative">
        
        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-red-600 to-red-400"></div>

        <div class="p-8 md:p-10 border-b border-gray-100 bg-gray-50/50 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-gray-900 tracking-tight">Nota Pembelian Masuk</h2>
                    <p class="text-gray-500 font-medium mt-1">
                        ID Transaksi: <span class="font-bold text-gray-800 bg-gray-200 px-2 py-0.5 rounded-md">#PRC-{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </p>
                </div>
            </div>
            
            <div class="text-left md:text-right bg-white p-4 rounded-2xl border border-gray-100 shadow-sm w-full md:w-auto">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Tanggal Masuk</p>
                <p class="text-lg font-black text-gray-800 flex items-center gap-2">
                    <i class="fas fa-calendar-check text-red-500"></i>
                    {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d F Y') }}
                </p>
            </div>
        </div>

        <div class="p-8 md:p-10 border-b border-gray-100 flex items-center gap-4">
            <div class="w-10 h-10 bg-gray-100 text-gray-500 rounded-xl flex items-center justify-center">
                <i class="fas fa-store"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Supplier / Toko Asal</p>
                <h3 class="text-xl font-black text-gray-800">{{ $purchase->supplier_name }}</h3>
            </div>
        </div>

        <div class="p-8 md:p-10">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                <i class="fas fa-box-open text-red-500"></i> Rincian Stok Masuk
            </h3>
            
            <div class="overflow-x-auto rounded-2xl border border-gray-200">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500 border-b border-gray-200">
                        <tr>
                            <th class="py-4 px-6 font-bold w-1/2">Item Suku Cadang</th>
                            <th class="py-4 px-6 font-bold text-center">Qty Masuk</th>
                            <th class="py-4 px-6 font-bold text-right">Harga Modal</th>
                            <th class="py-4 px-6 font-bold text-right">Subtotal</th>
                            <th class="py-4 px-4 font-bold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                        @forelse($purchase->details as $detail)
                        <tr class="hover:bg-gray-50/50 transition group">
                            <td class="py-5 px-6">
                                <div class="flex items-start gap-3">
                                    <div class="w-2 h-2 rounded-full bg-gray-300 mt-2 group-hover:bg-red-400 transition-colors"></div>
                                    <div class="flex flex-col">
                                        @if($detail->sparepart)
                                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-3 mb-1">
                                                <span class="font-bold text-gray-800 text-base">{{ $detail->sparepart->name }}</span>
                                                <span class="w-max text-[9px] uppercase tracking-widest bg-gray-100 border border-gray-200 text-gray-600 px-2 py-0.5 rounded font-bold">
                                                    {{ $detail->sparepart->category ?? 'Lainnya' }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="font-bold text-gray-500 text-base line-through opacity-70" title="Nama Historis">{{ $detail->historical_name }}</span>
                                            <span class="text-[10px] font-bold text-red-500 uppercase tracking-wider mt-0.5"><i class="fas fa-exclamation-triangle"></i> Dihapus dari Master Inventaris</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-5 px-6 text-center">
                                <span class="font-black text-green-700 bg-green-100 border border-green-200 px-3 py-1.5 rounded-xl inline-flex items-center gap-1 shadow-sm">
                                    <i class="fas fa-plus text-xs"></i>{{ $detail->quantity }}
                                </span>
                            </td>
                            <td class="py-5 px-6 text-right font-medium text-gray-500">
                                Rp {{ number_format($detail->historical_price ?? $detail->price, 0, ',', '.') }}
                            </td>
                            <td class="py-5 px-6 text-right font-black text-gray-900 text-base">
                                Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                            </td>
                            <td class="py-5 px-4 text-center">
                                <form action="{{ route('admin.purchases.destroyItem', $detail->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus barang ini dari nota? Jika barang master masih ada, stoknya akan otomatis dikurangi.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 p-2 bg-gray-50 hover:bg-red-50 border border-transparent hover:border-red-200 rounded-lg transition-all shadow-sm" title="Hapus dari Nota">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400 italic font-medium">
                                Tidak ada rincian barang untuk transaksi ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-8 bg-gray-900 p-6 md:p-8 rounded-3xl flex flex-col md:flex-row justify-between items-center shadow-lg relative overflow-hidden">
                <div class="absolute -right-10 -top-10 text-white opacity-5 text-9xl pointer-events-none">
                    <i class="fas fa-wallet"></i>
                </div>
                
                <div class="relative z-10 flex items-center gap-3 mb-2 md:mb-0">
                    <div class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center text-white">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <span class="text-sm font-bold text-gray-300 uppercase tracking-widest">Total Pengeluaran Bengkel</span>
                </div>
                <div class="relative z-10 text-right">
                    <span class="text-4xl md:text-5xl font-black text-white tracking-tight">Rp {{ number_format($purchase->total_cost, 0, ',', '.') }}</span>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection