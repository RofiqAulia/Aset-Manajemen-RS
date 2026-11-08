<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AssetMovementStatus: string implements HasColor, HasLabel
{
    case MENUNGGU_PERSETUJUAN = 'menunggu_persetujuan';
    case DISETUJUI = 'disetujui';
    case DITOLAK = 'ditolak';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MENUNGGU_PERSETUJUAN => 'Menunggu Persetujuan',
            self::DISETUJUI => 'Disetujui',
            self::DITOLAK => 'Ditolak',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::MENUNGGU_PERSETUJUAN => 'warning',
            self::DISETUJUI => 'success',
            self::DITOLAK => 'danger',
        };
    }

    public function label(): string
    {
        return $this->getLabel() ?? '';
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::MENUNGGU_PERSETUJUAN => 'badge bg-warning text-dark',
            self::DISETUJUI => 'badge bg-success',
            self::DITOLAK => 'badge bg-danger',
        };
    }
}
