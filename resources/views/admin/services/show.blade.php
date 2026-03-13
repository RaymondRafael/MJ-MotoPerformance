@extends('admin.layouts.app')
@section('title', 'Detail Nota Servis')
@section('header', 'Detail Transaksi Servis')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('admin.services.index') }}" class="text-gray-500 hover:text-red-600 font-bold transition">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Antrean
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <div class="lg:col-span-1 space-y-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 border-b pb-2">Informasi Kendaraan</h3>
            <div class="text-center mb-6">
                <span class="inline-block px-4 py-2 bg-gray-900 text-white text-xl font-black rounded-lg tracking-widest mb-2 shadow-inner">
                    {{ $service->vehicle->license_plate }}
                </span>
                <p class="text-lg font-bold text-gray-800">{{ $service->vehicle->brand }} {{ $service->vehicle->model }}</p>
                <p class="text-sm text-gray-500">{{ $service->vehicle->color }}</p>
            </div>
            
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Pelanggan:</span>
                    <span class="font-bold text-gray-800">{{ $service->vehicle->customer->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">WhatsApp:</span>
                    <span class="font-bold text-green-600"><i class="fab fa-whatsapp mr-1"></i> {{ $service->vehicle->customer->phone_number }}</span>
                </div>
                <div class="flex justify-between border-t pt-3 mt-3">
                    <span class="text-gray-500">Mekanik:</span>
                    <span class="font-bold text-gray-800">{{ $service->mechanic->name ?? 'Belum Ditentukan' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Tanggal Masuk:</span>
                    <span class="font-bold text-gray-800">{{ $service->created_at->format('d M Y, H:i') }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 border-b pb-2">Keluhan & Status</h3>
            <p class="text-sm text-gray-800 bg-gray-50 p-3 rounded-lg border border-gray-200 mb-4 italic">
                "{{ $service->complaint }}"
            </p>

            <form action="{{ route('admin.services.updateStatus', $service->id) }}" method="POST">
                @csrf
                @method('PUT')
                <label class="block text-xs font-bold text-gray-500 mb-2">Ubah Status Pengerjaan:</label>
                <div class="flex gap-2">
                    <select name="status" class="flex-1 text-sm font-bold rounded-lg border-gray-300 px-3 py-2 bg-white shadow-sm focus:ring-red-500 focus:border-red-500">
                        <option value="pending" {{ $service->status == 'pending' ? 'selected' : '' }}>Menunggu Antrean</option>
                        <option value="processing" {{ $service->status == 'processing' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                        <option value="finished" {{ $service->status == 'finished' ? 'selected' : '' }}>Selesai (Kirim WA)</option>
                    </select>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-6">
        
        @if(session('error'))
            <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded mb-4">
                {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800"><i class="fas fa-file-invoice-dollar text-red-500 mr-2"></i>Rincian Biaya & Suku Cadang</h3>
            </div>

            <div class="p-6 border-b border-gray-100 bg-white">
                <form action="{{ route('admin.services.addSparepart', $service->id) }}" method="POST" class="flex flex-col sm:flex-row gap-4 items-end">
                    @csrf
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-gray-500 mb-2">Pilih Suku Cadang</label>
                        <select name="sparepart_id" required class="w-full text-sm rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500">
                            <option value="">-- Pilih Barang (Stok Tersedia) --</option>
                            @foreach($spareparts as $sp)
                                <option value="{{ $sp->id }}">{{ $sp->name }} - Rp {{ number_format($sp->price, 0, ',', '.') }} (Sisa: {{ $sp->stock }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-24">
                        <label class="block text-xs font-bold text-gray-500 mb-2">Jumlah</label>
                        <input type="number" name="quantity" value="1" min="1" required class="w-full text-sm rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500 text-center">
                    </div>
                    <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-bold transition h-[42px]">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                </form>
            </div>

            <div class="p-6">
                <table class="w-full text-left border-collapse mb-6">
                    <thead>
                        <tr class="border-b border-gray-200 text-xs uppercase text-gray-500">
                            <th class="py-2 font-bold">Item / Keterangan</th>
                            <th class="py-2 font-bold text-center">Qty</th>
                            <th class="py-2 font-bold text-right">Harga</th>
                            <th class="py-2 font-bold text-right">Subtotal</th>
                            <th class="py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                        
                        <tr class="bg-gray-50">
                            <td class="py-3 px-2 font-bold text-gray-800 border-l-4 border-blue-500">Biaya Jasa Mekanik</td>
                            <td class="py-3 text-center">-</td>
                            <td class="py-3 text-right text-gray-400">Manual</td>
                            <td class="py-3 text-right">
                                <form action="{{ route('admin.services.updateCost', $service->id) }}" method="POST" class="flex justify-end items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <span class="font-bold text-gray-600">Rp</span>
                                    <input type="number" name="service_cost" value="{{ $service->service_cost }}" min="0" class="w-28 text-sm font-bold text-right rounded border-gray-300 py-1 focus:ring-blue-500 focus:border-blue-500">
                                    <button type="submit" class="text-white bg-blue-500 hover:bg-blue-600 px-2 py-1 rounded text-xs transition">Simpan</button>
                                </form>
                            </td>
                            <td></td>
                        </tr>

                        @foreach($service->details as $detail)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-2">{{ $detail->sparepart->name }}</td>
                            <td class="py-3 text-center font-bold">{{ $detail->quantity }}</td>
                            <td class="py-3 text-right">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                            <td class="py-3 text-right font-bold text-gray-900">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            <td class="py-3 text-center">
                                <form action="{{ route('admin.services.removeSparepart', ['id' => $service->id, 'detail_id' => $detail->id]) }}" method="POST" class="inline" onsubmit="return confirm('Hapus suku cadang ini dari nota? Stok akan dikembalikan otomatis.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-1"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>

                <div class="bg-red-50 p-4 rounded-xl border border-red-200 flex justify-between items-center shadow-inner">
                    <span class="text-sm font-black text-red-800 uppercase tracking-widest">Total Tagihan</span>
                    <span class="text-3xl font-black text-red-600">Rp {{ number_format($service->total_cost, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection