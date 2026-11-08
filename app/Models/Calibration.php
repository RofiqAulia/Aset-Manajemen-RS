<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CalibrationResult;
use App\Support\DocumentNumberGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Calibration extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'asset_id',
        'vendor_id',
        'calibration_date',
        'expiry_date',
        'certificate_number',
        'result',
        'cost',
        'tested_by',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'calibration_date' => 'date',
            'expiry_date' => 'date',
            'result' => CalibrationResult::class,
            'cost' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $calibration): void {
            if (empty($calibration->certificate_number)) {
                $calibration->certificate_number = DocumentNumberGenerator::generateCalibrationCertificateNumber();
            }
        });
    }

    /**
     * Relasi ke aset medis yang dikalibrasi.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Relasi ke vendor / lembaga penguji kalibrasi (misal BPFK / pihak ketiga).
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Cek apakah masa berlaku sertifikat kalibrasi sudah kedaluwarsa.
     */
    public function isExpired(): bool
    {
        return $this->expiry_date->isPast();
    }

    /**
     * Cek apakah sertifikat kalibrasi akan segera kedaluwarsa dalam rentang hari tertentu.
     */
    public function isExpiringSoon(int $days = 30): bool
    {
        return ! $this->isExpired() && $this->expiry_date->diffInDays(now()) <= $days;
    }
}
