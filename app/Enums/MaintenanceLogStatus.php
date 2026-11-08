<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum MaintenanceLogStatus: string implements HasColor, HasLabel
{
    case DIJADWALKAN = 'dijadwalkan';
    case SELESAI = 'selesai';
    case TERLAMBAT = 'terlambat';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DIJADWALKAN => 'Dijadwalkan',
            self::SELESAI => 'Selesai',
            self::TERLAMBAT => 'Terlambat',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DIJADWALKAN => 'info',
            self::SELESAI => 'success',
            self::TERLAMBAT => 'danger',
        };
    }

    public function label(): string
    {
        return $this->getLabel() ?? '';
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DIJADWALKAN => 'badge bg-info',
            self::SELESAI => 'badge bg-success',
            self::TERLAMBAT => 'badge bg-danger',
        };
    }
}
