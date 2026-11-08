<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum WorkOrderStatus: string implements HasColor, HasLabel
{
    case BARU = 'baru';
    case DITUGASKAN = 'ditugaskan';
    case DIKERJAKAN = 'dikerjakan';
    case MENUNGGU_SUKU_CADANG = 'menunggu_suku_cadang';
    case SELESAI = 'selesai';
    case DIBATALKAN = 'dibatalkan';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::BARU => 'Baru',
            self::DITUGASKAN => 'Ditugaskan',
            self::DIKERJAKAN => 'Dikerjakan',
            self::MENUNGGU_SUKU_CADANG => 'Menunggu Suku Cadang',
            self::SELESAI => 'Selesai',
            self::DIBATALKAN => 'Dibatalkan',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::BARU => 'info',
            self::DITUGASKAN => 'primary',
            self::DIKERJAKAN => 'warning',
            self::MENUNGGU_SUKU_CADANG => 'gray',
            self::SELESAI => 'success',
            self::DIBATALKAN => 'danger',
        };
    }

    public function label(): string
    {
        return $this->getLabel() ?? '';
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::BARU => 'badge bg-info',
            self::DITUGASKAN => 'badge bg-primary',
            self::DIKERJAKAN => 'badge bg-warning text-dark',
            self::MENUNGGU_SUKU_CADANG => 'badge bg-secondary',
            self::SELESAI => 'badge bg-success',
            self::DIBATALKAN => 'badge bg-danger',
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::BARU => in_array($target, [self::DITUGASKAN, self::DIBATALKAN], true),
            self::DITUGASKAN => in_array($target, [self::DIKERJAKAN, self::DIBATALKAN, self::DITUGASKAN], true),
            self::DIKERJAKAN => in_array($target, [self::MENUNGGU_SUKU_CADANG, self::SELESAI], true),
            self::MENUNGGU_SUKU_CADANG => $target === self::DIKERJAKAN,
            self::SELESAI => $target === self::DIKERJAKAN,
            self::DIBATALKAN => false,
        };
    }
}
