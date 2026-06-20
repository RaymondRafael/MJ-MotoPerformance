<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('spareparts', function (Blueprint $table) {
            // Menambahkan kolom kategori setelah kolom nama
            $table->string('category')->default('Lainnya')->after('name');
        });
    }

    public function down()
    {
        Schema::table('spareparts', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};