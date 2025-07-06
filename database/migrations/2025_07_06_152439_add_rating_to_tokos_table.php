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
        Schema::table('tokos', function (Blueprint $table) {
            // Kolom untuk menyimpan rata-rata rating dari semua produknya
            $table->decimal('rating', 2, 1)->default(0.0)->after('image');
            // Kolom untuk menyimpan jumlah produk yang dimiliki (opsional, tapi berguna)
            $table->unsignedInteger('products_count')->default(0)->after('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tokos', function (Blueprint $table) {
            $table->dropColumn(['rating', 'products_count']);
        });
    }
};
