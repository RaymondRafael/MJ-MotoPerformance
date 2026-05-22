<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::query();

        // Filter Pencarian Supplier
        if ($request->filled('search')) {
            $query->where('supplier_name', 'like', '%' . $request->search . '%');
        }

        // Filter Tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('purchase_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('purchase_date', '<=', $request->end_date);
        }

        // Urutkan dari yang terbaru dan paginasi
        $purchases = $query->orderBy('purchase_date', 'desc')->paginate(10);

        return view('admin.purchases.index', compact('purchases'));
    }

    public function create() 
    {
        // Karena kita menggunakan SoftDeletes/Snapshot, 
        // pastikan form hanya menampilkan barang yang BELUM dihapus (stok aktif)
        $spareparts = Sparepart::all(); 
        return view('admin.purchases.create', compact('spareparts'));
    }

    public function store(Request $request)
    {
        // 1. Validasi data form utama
        $request->validate([
            'supplier_name' => 'required',
            'purchase_date' => 'required|date',
            'items' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            // 2. Buat data struk pembelian
            $purchase = Purchase::create([
                'supplier_name' => $request->supplier_name,
                'purchase_date' => $request->purchase_date,
                'total_cost' => 0
            ]);

            $grandTotal = 0;

            // 3. Looping setiap barang yang dibeli
            foreach ($request->items as $index => $item) {
                $sparepartId = null;
                $snapshotName = '';

                // BENTENG PERTAHANAN: Bersihkan titik dari angka di sisi Server (berjaga-jaga jika JS gagal)
                $qty = (int) str_replace('.', '', $item['quantity']);
                $price = (int) str_replace('.', '', $item['price']); // Ini adalah harga MODAL/BELI

                if ($item['mode'] === 'new') {
                    // Cek jika admin memilih 'Barang Baru' tapi lupa mengisi namanya
                    if (empty($item['new_name']) || empty($item['selling_price'])) {
                        throw new \Exception("Nama Barang dan Harga Jual pada baris ke-" . ($index + 1) . " wajib diisi.");
                    }

                    // --- BENTENG ANTI-DUPLIKAT (TAMBAHKAN KODE INI) ---
                    // Bersihkan spasi berlebih di awal/akhir nama barang
                    $namaBarangBaru = trim($item['new_name']); 
                    
                    // Cek ke database, apakah sudah ada nama yang persis sama?
                    // (Secara otomatis mengabaikan huruf besar/kecil di MySQL)
                    $cekDuplikat = Sparepart::where('name', $namaBarangBaru)->first();

                    if ($cekDuplikat) {
                        throw new \Exception("Gagal: Barang dengan nama '{$namaBarangBaru}' sudah terdaftar di inventaris! Silakan batalkan transaksi ini dan gunakan mode 'Barang Lama'.");
                    }
                    // ---------------------------------------------------

                    $sellingPrice = (int) str_replace('.', '', $item['selling_price']); // Ini adalah harga JUAL

                    // Buat barang baru di tabel Master
                    $newSparepart = Sparepart::create([
                        'name' => $namaBarangBaru, // Gunakan nama yang sudah di-trim
                        'price' => $sellingPrice,
                        'stock' => $qty,
                    ]);
                    
                    $sparepartId = $newSparepart->id;
                    $snapshotName = $newSparepart->name; 

                } else {
                    // Cek jika admin memilih 'Barang Lama' tapi lupa memilih barang dari Dropdown
                    if (empty($item['sparepart_id'])) {
                        throw new \Exception("Suku cadang pada baris ke-" . ($index + 1) . " belum dipilih.");
                    }

                    $sparepartId = $item['sparepart_id'];
                    $sparepart = Sparepart::findOrFail($sparepartId);

                    // EKSEKUSI TAMBAH STOK BARANG LAMA
                    $sparepart->increment('stock', $qty);
                    
                    $snapshotName = $sparepart->name; // Simpan snapshot nama dari database saat itu
                }

                $subtotal = $qty * $price;
                $grandTotal += $subtotal;

                // 4. Catat riwayat barang masuk ini (Beserta SNAPSHOT-nya)
                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'sparepart_id' => $sparepartId,
                    'historical_name' => $snapshotName,   // <-- Fitur Snapshot Data
                    'historical_price' => $price,         // <-- Fitur Snapshot Data
                    'quantity' => $qty,
                    'price' => $price, // Menyimpan price untuk kompatibilitas kode lama jika ada
                    'subtotal' => $subtotal
                ]);
            }

            // 5. Simpan Total Akhir Pembelian
            $purchase->update(['total_cost' => $grandTotal]);

            DB::commit();
            return redirect()->route('admin.purchases.index')->with('success', 'Pembelian dicatat & Stok berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Transaksi dibatalkan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        // with() sekarang juga siap menangani kalau sparepart_id-nya null (karena sudah dihapus)
        $purchase = Purchase::with('details.sparepart')->findOrFail($id);
        return view('admin.purchases.show', compact('purchase'));
    }

    public function destroyItem($detail_id) {
        $purchaseDetail = PurchaseDetail::find($detail_id);
        
        // Cek jika barang master-nya masih ada (belum kena Hard Delete SET NULL)
        if ($purchaseDetail->sparepart_id) {
            $sparepart = Sparepart::find($purchaseDetail->sparepart_id);
            if ($sparepart) {
                // Kurangi stok jika barang batal dibeli
                $sparepart->stock -= $purchaseDetail->quantity;
                $sparepart->save();
            }
        }

        // Hapus baris dari nota tersebut
        $purchaseDetail->delete(); 
        
        return back()->with('success', 'Barang berhasil dihapus dari nota pembelian.');
    }
}