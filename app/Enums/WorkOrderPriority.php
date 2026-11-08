<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum WorkOrderPriority: string implements HasColor, HasLabel
{
    case RENDAH = 'rendah';
    case SEDANG = 'sedang';
    case TINGGI = 'tinggi';
    case KRITIS = 'kritis';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::RENDAH => 'Rendah',
            self::SEDANG => 'Sedang',
            self::TINGGI => 'Tinggi',
            self::KRITIS => 'Kritis',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::RENDAH => 'gray',
            self::SEDANG => 'info',
            self::TINGGI => 'warning',
            self::KRITIS => 'danger',
        };
    }

    public function label(): string
    {
        return $this->getLabel() ?? '';
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::RENDAH => 'badge bg-secondary',
            self::SEDANG => 'badge bg-primary',
            self::TINGGI => 'badge bg-warning text-dark',
            self::KRITIS => 'badge bg-danger',
        };
    }
}
