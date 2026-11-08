<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Calibration;
use App\Models\Category;
use App\Models\Department;
use App\Models\Location;
use App\Models\StockOpname;
use App\Models\WorkOrder;

class DocumentNumberGenerator
{
    /**
     * Generate format kode unik aset: AST-YYYY-NNNNNN (contoh: AST-2026-000001)
     */
    public static function generateAssetCode(?int $year = null): string
    {
        return AssetCodeGenerator::generate($year);
    }

    /**
     * Generate format nomor Stock Opname: SO-YYYYMM-NNNN (contoh: SO-202611-0001)
     */
    public static function generateStockOpnameNumber(?string $yearMonth = null): string
    {
        $yearMonth = $yearMonth ?? date('Ym');
        $prefix = "SO-{$yearMonth}-";

        $lastRecord = StockOpname::withTrashed()
            ->where('opname_number', 'like', "{$prefix}%")
            ->orderByDesc('opname_number')
            ->first();

        $nextSequence = 1;

        if ($lastRecord && preg_match('/SO-\d{6}-(\d+)/', $lastRecord->opname_number, $matches)) {
            $nextSequence = (int) $matches[1] + 1;
        }

        return sprintf('%s%04d', $prefix, $nextSequence);
    }

    /**
     * Generate format nomor Work Order: WO-YYYYMM-NNNN (contoh: WO-202611-0001)
     */
    public static function generateWorkOrderNumber(?string $yearMonth = null): string
    {
        $yearMonth = $yearMonth ?? date('Ym');
        $prefix = "WO-{$yearMonth}-";

        $lastRecord = WorkOrder::withTrashed()
            ->where('wo_number', 'like', "{$prefix}%")
            ->orderByDesc('wo_number')
            ->first();

        $nextSequence = 1;

        if ($lastRecord && preg_match('/WO-\d{6}-(\d+)/', $lastRecord->wo_number, $matches)) {
            $nextSequence = (int) $matches[1] + 1;
        }

        return sprintf('%s%04d', $prefix, $nextSequence);
    }

    /**
     * Generate format nomor sertifikat kalibrasi: CAL-YYYYMM-NNNN (contoh: CAL-202611-0001)
     */
    public static function generateCalibrationCertificateNumber(?string $yearMonth = null): string
    {
        $yearMonth = $yearMonth ?? date('Ym');
        $prefix = "CAL-{$yearMonth}-";

        $lastRecord = Calibration::withTrashed()
            ->where('certificate_number', 'like', "{$prefix}%")
            ->orderByDesc('certificate_number')
            ->first();

        $nextSequence = 1;

        if ($lastRecord && preg_match('/CAL-\d{6}-(\d+)/', (string) $lastRecord->certificate_number, $matches)) {
            $nextSequence = (int) $matches[1] + 1;
        }

        return sprintf('%s%04d', $prefix, $nextSequence);
    }

    /**
     * Generate format kode kategori: CAT-NNN (contoh: CAT-001)
     */
    public static function generateCategoryCode(): string
    {
        $prefix = 'CAT-';
        $maxSeq = Category::where('code', 'like', "{$prefix}%")
            ->pluck('code')
            ->map(function ($code) {
                return preg_match('/CAT-(\d+)/', (string) $code, $matches) ? (int) $matches[1] : 0;
            })
            ->max() ?? 0;

        return sprintf('%s%03d', $prefix, $maxSeq + 1);
    }

    /**
     * Generate format kode ruangan / lokasi: LOC-NNN (contoh: LOC-001)
     */
    public static function generateLocationCode(): string
    {
        $prefix = 'LOC-';
        $maxSeq = Location::where('code', 'like', "{$prefix}%")
            ->pluck('code')
            ->map(function ($code) {
                return preg_match('/LOC-(\d+)/', (string) $code, $matches) ? (int) $matches[1] : 0;
            })
            ->max() ?? 0;

        return sprintf('%s%03d', $prefix, $maxSeq + 1);
    }

    /**
     * Generate format kode departemen / unit: DEP-NNN (contoh: DEP-001)
     */
    public static function generateDepartmentCode(): string
    {
        $prefix = 'DEP-';
        $maxSeq = Department::where('code', 'like', "{$prefix}%")
            ->pluck('code')
            ->map(function ($code) {
                return preg_match('/DEP-(\d+)/', (string) $code, $matches) ? (int) $matches[1] : 0;
            })
            ->max() ?? 0;

        return sprintf('%s%03d', $prefix, $maxSeq + 1);
    }
}
