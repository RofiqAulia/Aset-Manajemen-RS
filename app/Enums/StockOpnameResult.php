<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StockOpnameResult: string implements HasColor, HasLabel
{
    case DITEMUKAN = 'ditemukan';
    case KONDISI_BERBEDA = 'kondisi_berbeda';
    case LOKASI_BERBEDA = 'lokasi_berbeda';
    case TIDAK_DITEMUKAN = 'tidak_ditemukan';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DITEMUKAN => 'Ditemukan Sesuai',
            self::KONDISI_BERBEDA => 'Kondisi Berbeda',
            self::LOKASI_BERBEDA => 'Lokasi Berbeda',
            self::TIDAK_DITEMUKAN => 'Tidak Ditemukan',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DITEMUKAN => 'success',
            self::KONDISI_BERBEDA => 'warning',
            self::LOKASI_BERBEDA => 'info',
            self::TIDAK_DITEMUKAN => 'danger',
        };
    }

    public function label(): string
    {
        return $this->getLabel() ?? '';
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DITEMUKAN => 'badge bg-success',
            self::KONDISI_BERBEDA => 'badge bg-warning text-dark',
            self::LOKASI_BERBEDA => 'badge bg-info text-dark',
            self::TIDAK_DITEMUKAN => 'badge bg-danger',
        };
    }
}
