<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AssetStatus: string implements HasColor, HasLabel
{
    case AKTIF = 'aktif';
    case DALAM_PERBAIKAN = 'dalam_perbaikan';
    case NON_AKTIF = 'non_aktif';
    case HILANG = 'hilang';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::AKTIF => 'Aktif',
            self::DALAM_PERBAIKAN => 'Dalam Perbaikan',
            self::NON_AKTIF => 'Non Aktif',
            self::HILANG => 'Hilang',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::AKTIF => 'success',
            self::DALAM_PERBAIKAN => 'warning',
            self::NON_AKTIF => 'gray',
            self::HILANG => 'danger',
        };
    }

    public function label(): string
    {
        return $this->getLabel() ?? '';
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::AKTIF => 'badge bg-success',
            self::DALAM_PERBAIKAN => 'badge bg-warning text-dark',
            self::NON_AKTIF => 'badge bg-secondary',
            self::HILANG => 'badge bg-danger',
        };
    }
}
