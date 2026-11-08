<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MaintenanceLogStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceLog extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'maintenance_schedule_id',
        'asset_id',
        'technician_id',
        'performed_at',
        'status',
        'cost',
        'findings',
        'action_taken',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'performed_at' => 'date',
            'status' => MaintenanceLogStatus::class,
            'cost' => 'decimal:2',
        ];
    }

    /**
     * Relasi ke jadwal pemeliharaan induk.
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(MaintenanceSchedule::class, 'maintenance_schedule_id');
    }

    /**
     * Relasi ke aset yang dipelihara.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Relasi ke teknisi yang melaksanakan perawatan preventif.
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}
