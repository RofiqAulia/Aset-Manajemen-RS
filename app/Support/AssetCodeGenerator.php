<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Asset;

class AssetCodeGenerator
{
    /**
     * Generate format kode unik: AST-YYYY-NNNNNN (contoh: AST-2026-000001)
     */
    public static function generate(?int $year = null): string
    {
        $year = $year ?? (int) date('Y');
        $prefix = "AST-{$year}-";

        $lastAsset = Asset::withTrashed()
            ->where('asset_code', 'like', "{$prefix}%")
            ->orderByDesc('asset_code')
            ->first();

        $nextSequence = 1;

        if ($lastAsset) {
            $lastCode = $lastAsset->asset_code;
            $parts = explode('-', $lastCode);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSequence = (int) $parts[2] + 1;
            }
        }

        return sprintf('%s%06d', $prefix, $nextSequence);
    }
}
