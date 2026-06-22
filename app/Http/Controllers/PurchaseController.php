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

        // 1. Pencarian berdasarkan nama supplier
        if ($request->filled('search')) {
            $query->where('supplier_name', 'like', '%' . $request->search . '%');
        }

        // 2. Filter berdasarkan Bulan
        if ($request->filled('month')) {
            $query->whereMonth('purchase_date', $request->month);
        }

        // 3. Filter berdasarkan Tahun
        if ($request->filled('year')) {
            $query->whereYear('purchase_date', $request->year);
        }

        $purchases = $query->orderBy('purchase_date', 'desc')->orderBy('id', 'desc')->paginate(10);

        // Array untuk menampilkan nama bulan
        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        $years = Purchase::selectRaw('YEAR(purchase_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
            
        if (empty($years)) {
            $years = [date('Y')];
        }

        return view('admin.purchases.index', compact('purchases', 'months', 'years'));
    }

    public function create() 
    {
        $spareparts = Sparepart::all(); 
        
        // Array untuk kategori suku cadang yang akan digunakan di form tambah pembelian
        $categories = ['Oli', 'Shockbreaker', 'Roller', 'Vanbelt', 'Busi', 'Sistem Rem', 'Ban', 'Air Radiator', 'Handgrip', 'Lainnya'];
        
        return view('admin.purchases.create', compact('spareparts', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required',
            'purchase_date' => 'required|date',
            'items' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $purchase = Purchase::create([
                'supplier_name' => $request->supplier_name,
                'purchase_date' => $request->purchase_date,
                'total_cost' => 0
            ]);

            $grandTotal = 0;

            foreach ($request->items as $index => $item) {
                $sparepartId = null;
                $snapshotName = '';

                $qty = (int) str_replace('.', '', $item['quantity']);
                $price = (int) str_replace('.', '', $item['price']); 

                if ($qty <= 0) {
                    throw new \Exception("Kuantitas barang pada baris ke-" . ($index + 1) . " tidak boleh 0.");
                }

                if ($price <= 0) {
                    throw new \Exception("Harga Beli (Modal) pada baris ke-" . ($index + 1) . " tidak boleh 0.");
                }

                if ($item['mode'] === 'new') {
                    // Cek juga apakah kategori dipilih
                    if (empty($item['new_name']) || empty($item['selling_price']) || empty($item['new_category'])) {
                        throw new \Exception("Nama Barang, Kategori, dan Harga Jual pada baris ke-" . ($index + 1) . " wajib diisi.");
                    }



                    // Untuk mencegah duplikasi barang baru yang sangat mirip dengan barang lama.
                    $namaBarangBaru = trim($item['new_name']); 
                    $cekDuplikat = null;
                    
                    // Pengecekan sama persis
                    $cekDuplikat = Sparepart::where('name', $namaBarangBaru)->first();

                    // Pengecekan Kata per Kata
                    if (!$cekDuplikat) {
                        
                        // Fungsi memecah nama jadi kata-kata (tanpa simbol)
                        $normalizeToWords = function($str) {
                            $str = str_replace(['-', '.', '\''], '', strtolower($str)); 
                            $words = preg_split('/[^a-z0-9]/', $str, -1, PREG_SPLIT_NO_EMPTY);
                            return array_values(array_unique($words));
                        };

                        $newWords = $normalizeToWords($namaBarangBaru);
                        
                        // Jika nama baru tidak memiliki kata sama sekali, lewati pengecekan kata per kata
                        if (count($newWords) > 0) {
                            $allSpareparts = Sparepart::select('id', 'name')->get();
                            
                            // Bandingkan kata per kata dengan semua sparepart yang ada
                            foreach($allSpareparts as $sp) {
                                $existingWords = $normalizeToWords($sp->name);
                                
                                // Lewati jika data lama tidak ada huruf/angkanya
                                if (count($existingWords) === 0) continue;
                                
                                // Tentukan array mana yang jumlah katanya lebih sedikit / lebih banyak
                                $shorter = count($newWords) < count($existingWords) ? $newWords : $existingWords;
                                $longer = count($newWords) < count($existingWords) ? $existingWords : $newWords;
                                
                                $matchedWords = 0;

                                // Cek setiap kata di array yang lebih pendek
                                foreach ($shorter as $shortWord) {
                                    $bestMatchPercent = 0;
                                    
                                    // Bandingkan dengan seluruh kata di array yang lebih panjang
                                    foreach ($longer as $longWord) {
                                        similar_text($shortWord, $longWord, $percent);
                                        if ($percent > $bestMatchPercent) {
                                            $bestMatchPercent = $percent;
                                        }
                                    }
                                    
                                    // Jika kata ini memiliki kemiripan >= 80%, anggap ini kata yang sama
                                    if ($bestMatchPercent >= 80) {
                                        $matchedWords++;
                                    }
                                }
                                
                                // Jika seluruh kata di array yang lebih pendek berhasil "ditemukan" kemiripannya
                                if ($matchedWords === count($shorter)) {
                                    $cekDuplikat = $sp;
                                    break;
                                }
                            }
                        }
                    }

                    if ($cekDuplikat) {
                        throw new \Exception("Gagal: Barang yang Anda masukkan sangat identik/terkandung dalam '{$cekDuplikat->name}' yang sudah ada! Silakan gunakan mode 'Barang Lama'. Jika ini barang berbeda, ubah nama agar lebih spesifik (misal: tambah kode barang).");
                    }
                    // ---------------------------------------------------

                    $sellingPrice = (int) str_replace('.', '', $item['selling_price']); 

                    if ($sellingPrice <= 0) {
                        throw new \Exception("Harga Jual pada baris ke-" . ($index + 1) . " tidak boleh 0.");
                    }

                    if ($sellingPrice < $price) {
                        throw new \Exception("Gagal pada baris ke-" . ($index + 1) . ": Harga Jual (Rp " . number_format($sellingPrice, 0, ',', '.') . ") tidak boleh lebih kecil dari Harga Modal/Beli (Rp " . number_format($price, 0, ',', '.') . ").");
                    }


                    // Menyimpan data kategori yang dipilih admin
                    $newSparepart = Sparepart::create([
                        'name' => $namaBarangBaru,
                        'brand'    => $item['brand'] ?? null,
                        'category' => $item['new_category'], 
                        'price' => $sellingPrice,
                        'stock' => $qty,
                    ]);
                    
                    $sparepartId = $newSparepart->id;
                    $snapshotName = $newSparepart->name; 

                } else {
                    if (empty($item['sparepart_id'])) {
                        throw new \Exception("Suku cadang pada baris ke-" . ($index + 1) . " belum dipilih.");
                    }

                    $sparepartId = $item['sparepart_id'];
                    $sparepart = Sparepart::findOrFail($sparepartId);

                    // Harga beli tidak boleh melebihi harga jual saat ini
                    if ($price > $sparepart->price) {
                        throw new \Exception("Gagal pada baris ke-" . ($index + 1) . ": Harga Beli/Modal (Rp " . number_format($price, 0, ',', '.') . ") tidak boleh melebihi Harga Jual saat ini (Rp " . number_format($sparepart->price, 0, ',', '.') . ").");
                    }

                    $sparepart->increment('stock', $qty);
                    $snapshotName = $sparepart->name;

                    $sparepart->increment('stock', $qty);
                    $snapshotName = $sparepart->name; 
                }

                $subtotal = $qty * $price;
                $grandTotal += $subtotal;

                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'sparepart_id' => $sparepartId,
                    'historical_name' => $snapshotName,   
                    'historical_price' => $price,         
                    'quantity' => $qty,
                    'price' => $price, 
                    'subtotal' => $subtotal
                ]);
            }

            $purchase->update(['total_cost' => $grandTotal]);

            DB::commit();
            return redirect()->route('admin.purchases.index')->with('success', 'Pembelian dicatat & Stok berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Transaksi dibatalkan: ' . $e->getMessage());
        }
    }

    // Menampilkan detail pembelian
    public function show($id)
    {
        $purchase = Purchase::with('details.sparepart')->findOrFail($id);
        return view('admin.purchases.show', compact('purchase'));
    }

    // Menghapus Item dari Nota Pembelian
    public function destroyItem($detail_id) {
        $purchaseDetail = PurchaseDetail::find($detail_id);
        
        if ($purchaseDetail->sparepart_id) {
            $sparepart = Sparepart::find($purchaseDetail->sparepart_id);
            if ($sparepart) {
                $sparepart->stock -= $purchaseDetail->quantity;
                $sparepart->save();
            }
        }

        $purchaseDetail->delete(); 
        return back()->with('success', 'Barang berhasil dihapus dari nota pembelian.');
    }
}