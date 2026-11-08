<?php

declare(strict_types=1);

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
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->decimal('total_variance_value', 15, 2)->default(0)->after('total_discrepancy');
            $table->decimal('total_accuracy_rate', 5, 2)->default(100.00)->after('total_variance_value');
            $table->decimal('physical_progress_rate', 5, 2)->default(0.00)->after('total_accuracy_rate');
        });

        Schema::table('stock_opname_items', function (Blueprint $table) {
            $table->integer('expected_stock')->default(1)->after('asset_id');
            $table->integer('actual_stock')->nullable()->after('expected_stock');
            $table->integer('discrepancy_quantity')->default(0)->after('actual_stock');
            $table->decimal('discrepancy_value', 15, 2)->default(0)->after('discrepancy_quantity');
            $table->string('status', 30)->default('belum_diperiksa')->after('result'); // sesuai, selisih, belum_diperiksa, hitung_ulang
            $table->string('root_cause', 50)->nullable()->after('status'); // salah_input, barang_rusak, salah_lokasi, hilang_pencurian, selisih_fisik, lainnya
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_opname_items', function (Blueprint $table) {
            $table->dropColumn([
                'expected_stock',
                'actual_stock',
                'discrepancy_quantity',
                'discrepancy_value',
                'status',
                'root_cause',
            ]);
        });

        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->dropColumn([
                'total_variance_value',
                'total_accuracy_rate',
                'physical_progress_rate',
            ]);
        });
    }
};
