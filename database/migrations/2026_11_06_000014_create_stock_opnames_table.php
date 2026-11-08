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
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('opname_number', 50)->unique();
            $table->string('title');
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->foreignId('auditor_id')->constrained('users');
            $table->string('status', 30)->default('berjalan'); // berjalan, selesai, dibatalkan
            $table->integer('total_expected')->default(0);
            $table->integer('total_scanned')->default(0);
            $table->integer('total_discrepancy')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained('stock_opnames')->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->foreignId('expected_location_id')->constrained('locations');
            $table->foreignId('actual_location_id')->nullable()->constrained('locations');
            $table->string('expected_condition', 20)->default('baik');
            $table->string('actual_condition', 20)->nullable();
            $table->string('result', 30)->default('ditemukan'); // ditemukan, kondisi_berbeda, lokasi_berbeda, tidak_ditemukan
            $table->timestamp('scanned_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
        Schema::dropIfExists('stock_opnames');
    }
};
