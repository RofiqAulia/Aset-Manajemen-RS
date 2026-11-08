<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AssetCondition: string implements HasColor, HasLabel
{
    case BAIK = 'baik';
    case RUSAK = 'rusak';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::BAIK => 'Baik',
            self::RUSAK => 'Rusak',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::BAIK => 'success',
            self::RUSAK => 'danger',
        };
    }

    public function label(): string
    {
        return $this->getLabel() ?? '';
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::BAIK => 'badge bg-success',
            self::RUSAK => 'badge bg-danger',
        };
    }
}
