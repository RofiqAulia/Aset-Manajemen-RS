<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum NotificationChannel: string implements HasColor, HasLabel
{
    case DATABASE = 'database';
    case MAIL = 'mail';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DATABASE => 'Notifikasi Sistem',
            self::MAIL => 'Email',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DATABASE => 'success',
            self::MAIL => 'info',
        };
    }

    public function label(): string
    {
        return $this->getLabel() ?? '';
    }
}
