<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabel Utama Pembelian
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name');
            $table->date('purchase_date');
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->onDelete('cascade');
            
            // 1. Jadikan nullable agar relasi bisa diputus
            $table->unsignedBigInteger('sparepart_id')->nullable(); 
            
            // 2. Set foreign key dengan aksi 'set null'
            $table->foreign('sparepart_id')->references('id')->on('spareparts')->onDelete('set null');
            
            // 3. Kolom Snapshot (Disarankan nullable sebagai jaring pengaman tambahan)
            // $table->string('historical_name')->nullable(); 
            $table->integer('historical_price')->nullable();
            
            // 4. Kolom Transaksi Standar (INI YANG SEBELUMNYA HILANG)
            $table->integer('quantity');
            $table->integer('price');
            $table->integer('subtotal');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
