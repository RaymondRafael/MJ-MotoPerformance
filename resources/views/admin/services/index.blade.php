@extends('admin.layouts.app')

@section('title', 'Transaksi Servis')
@section('header', 'Manajemen Servis')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    
    <div class="p-6 border-b flex flex-col md:flex-row justify-between items-center bg-gray-50 gap-4">
        <h2 class="font-bold text-gray-700">Antrean Kendaraan Hari Ini</h2>
        
        <div class="flex items-center gap-3 w-full md:w-auto">
            <form action="{{ route('admin.services.index') }}" method="GET" class="flex w-full md:w-auto">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Plat/Nama/Keluhan..." 
                        class="px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-red-500 focus:border-red-500 text-sm w-full md:w-64">
                    <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-r-lg border border-l-0 border-gray-300 transition">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <a href="{{ route('admin.services.create') }}" class="bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-bold transition whitespace-nowrap">
                <i class="fas fa-plus mr-1"></i> Servis Baru
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="m-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded">
        {{ session('success') }}
    </div>
    @endif
    
    @if(session('error'))
    <div class="m-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded">
        {{ session('error') }}
    </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white border-b border-gray-100 text-xs uppercase text-gray-500">
                    <th class="p-4 font-bold text-center w-16">No.</th> 
                    <th class="p-4 font-bold">Plat Nomor</th>
                    <th class="p-4 font-bold">Pelanggan</th>
                    <th class="p-4 font-bold">Keluhan & Mekanik</th>
                    <th class="p-4 font-bold">Status</th>
                    <th class="p-4 font-bold text-center">Aksi Lanjutan</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                @forelse($services as $service)
                <tr class="hover:bg-gray-50 transition">
                    
                    <td class="p-4 font-bold text-gray-900 text-center">
                        {{ ($services->currentPage() - 1) * $services->perPage() + $loop->iteration }}
                    </td>
                    
                    <td class="p-4">
                        @if($service->vehicle)
                            <span class="font-bold text-gray-900">{{ $service->vehicle->license_plate }}</span><br>
                            <span class="text-xs text-gray-500">
                                {{ $service->vehicle->brand }} {{ $service->vehicle->model }}
                            </span>
                        @else
                            <span class="font-bold text-gray-400 line-through" title="Data kendaraan telah dihapus">
                                {{ $service->historical_license_plate ?? 'Data Dihapus' }}
                            </span><br>
                            <span class="text-xs text-gray-400 line-through" title="Data kendaraan telah dihapus">
                                {{ $service->historical_vehicle_motor ?? 'Tidak Diketahui' }}
                            </span>
                        @endif
                    </td>

                    <td class="p-4">
                        @if($service->vehicle && $service->vehicle->customer)
                            <span class="text-gray-900">{{ $service->vehicle->customer->name }}</span><br>
                            <span class="text-xs text-gray-500"><i class="fab fa-whatsapp text-green-500"></i> 
                                {{ $service->vehicle->customer->phone_number }}
                            </span>
                        @else
                            <span class="text-gray-400 line-through" title="Data pelanggan telah dihapus">
                                {{ $service->historical_customer_name ?? 'Pelanggan Dihapus' }}
                            </span><br>
                            <span class="text-xs text-gray-400 line-through" title="Data pelanggan telah dihapus">
                                <i class="fab fa-whatsapp text-gray-400"></i> {{ $service->historical_customer_phone ?? '-' }}
                            </span>
                        @endif
                    </td>

                    <td class="p-4 max-w-xs" title="{{ $service->complaint }}">
                        <div class="truncate mb-1.5">{{ $service->complaint }}</div>
                        
                        <div>
                            @if($service->mechanic)
                                <span class="text-[10px] text-blue-600 font-bold bg-blue-50 px-2 py-0.5 rounded border border-blue-100">
                                    <i class="fas fa-wrench mr-1"></i> {{ $service->mechanic->name }}
                                </span>
                            @elseif($service->historical_mechanic_name)
                                <span class="text-[10px] text-gray-400 font-bold bg-gray-100 px-2 py-0.5 rounded border border-gray-200 line-through" title="Mekanik telah dihapus">
                                    <i class="fas fa-wrench mr-1"></i> {{ $service->historical_mechanic_name }}
                                </span>
                            @else
                                <span class="text-[10px] text-red-500 font-bold bg-red-50 px-2 py-0.5 rounded border border-red-100">
                                    Belum Ditentukan
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="p-4">
                        @if($service->status == 'pending')
                            <span class="px-3 py-1.5 rounded-lg text-xs font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">Menunggu</span>
                        @elseif($service->status == 'processing')
                            <span class="px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200">Dikerjakan</span>
                        @elseif($service->status == 'finished')
                            <span class="px-3 py-1.5 rounded-lg text-xs font-bold bg-green-100 text-green-700 border border-green-200">Selesai (Siap Ambil)</span>
                        @elseif($service->status == 'lunas')
                            <span class="px-3 py-1.5 rounded-lg text-xs font-bold bg-purple-100 text-purple-700 border border-purple-200"><i class="fas fa-check-double mr-1"></i> Lunas</span>
                        @endif
                    </td>
                    <td class="p-4 text-center space-x-2 flex justify-center items-center">
                        
                        @if($service->status == 'processing')
                            <form action="{{ route('admin.services.updateStatus', $service->id) }}" method="POST" class="inline" onsubmit="return confirm('Tandai servis selesai dan kirim WA tagihan ke pelanggan?')">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="finished">
                                <button type="submit" class="text-white bg-blue-500 hover:bg-blue-600 px-3 py-2 rounded-lg text-xs font-bold transition shadow-sm">
                                    <i class="fas fa-wrench mr-1"></i> Selesaikan
                                </button>
                            </form>
                        @elseif($service->status == 'finished')
                            <form action="{{ route('admin.services.updateStatus', $service->id) }}" method="POST" class="inline" onsubmit="return confirm('Tandai pembayaran telah lunas dan kirim WA ucapan terima kasih?')">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="lunas">
                                <button type="submit" class="text-white bg-green-500 hover:bg-green-600 px-3 py-2 rounded-lg text-xs font-bold transition shadow-sm">
                                    <i class="fas fa-hand-holding-usd mr-1"></i> Lunasi
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('admin.services.show', $service->id) }}" class="text-blue-500 hover:text-blue-700 p-2 bg-blue-50 rounded-lg ml-1" title="Lihat Detail/Nota"><i class="fas fa-eye"></i></a>
                        
                        @if(!in_array($service->status, ['finished', 'lunas']))
                            <a href="{{ route('admin.services.edit', $service->id) }}" class="text-gray-500 hover:text-gray-700 p-2 bg-gray-100 rounded-lg" title="Edit Antrean"><i class="fas fa-edit"></i></a>
                        @endif
                        
                        @if(!in_array($service->status, ['finished', 'lunas']))
                            <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin membatalkan dan menghapus antrean servis ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 p-2 bg-red-50 rounded-lg" title="Batalkan/Hapus Antrean"><i class="fas fa-trash"></i></button>
                            </form>
                        @else
                            <button type="button" class="text-gray-400 p-2 bg-gray-100 rounded-lg cursor-not-allowed" title="Nota ini sudah dikunci permanen oleh sistem keuangan">
                                <i class="fas fa-lock"></i>
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-gray-400">Tidak ada data servis yang ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($services->hasPages())
        <div class="p-4 border-t border-gray-100 bg-gray-50">
            {{ $services->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection