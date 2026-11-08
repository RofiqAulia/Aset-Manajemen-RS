<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CalibrationResult: string implements HasColor, HasLabel
{
    case LULUS = 'lulus';
    case LULUS_BERSYARAT = 'lulus_bersyarat';
    case GAGAL = 'gagal';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::LULUS => 'Lulus',
            self::LULUS_BERSYARAT => 'Lulus Bersyarat',
            self::GAGAL => 'Gagal',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::LULUS => 'success',
            self::LULUS_BERSYARAT => 'warning',
            self::GAGAL => 'danger',
        };
    }

    public function label(): string
    {
        return $this->getLabel() ?? '';
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::LULUS => 'badge bg-success',
            self::LULUS_BERSYARAT => 'badge bg-warning text-dark',
            self::GAGAL => 'badge bg-danger',
        };
    }
}
