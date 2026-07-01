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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            
            // Ubah relasi vehicle menjadi nullable dan set null (bukan cascade)
            // Jadi kalau motor atau pelanggannya dihapus, nota ini tidak ikut terhapus, melainkan ID-nya saja yang jadi NULL.
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->onDelete('set null');
            
            $table->foreignId('mechanic_id')->nullable()->constrained('mechanics')->onDelete('set null');
            
            // Tambahkan kolom Snapshot untuk Pelanggan dan Kendaraan
            // $table->string('historical_customer_name')->nullable();
            // $table->string('historical_customer_phone')->nullable();
            // $table->string('historical_license_plate')->nullable();
            // $table->string('historical_vehicle_motor')->nullable();
            
            // Perekam jejak nama mekanik 
            $table->string('historical_mechanic_name')->nullable(); 
            
            $table->text('complaint');
            $table->string('status', 50)->default('pending'); 
            $table->integer('service_cost')->default(0);
            $table->integer('total_cost')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};