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
        Schema::table('produks', function (Blueprint $table) {
            $table->decimal('rating', 2, 1)->default(0.0)->after('kategori_id'); // ->after() opsional, untuk kerapian
            $table->unsignedInteger('reviews_count')->default(0)->after('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
             $table->dropColumn(['rating', 'reviews_count']);
        });
    }
};
