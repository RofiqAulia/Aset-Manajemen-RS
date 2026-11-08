<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceSchedule extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'asset_id',
        'title',
        'frequency_days',
        'last_performed_at',
        'next_due_date',
        'assigned_technician_id',
        'is_active',
        'instructions',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_performed_at' => 'date',
            'next_due_date' => 'date',
            'is_active' => 'boolean',
            'frequency_days' => 'integer',
        ];
    }

    /**
     * Relasi ke aset yang dijadwalkan pemeliharaannya.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Relasi ke teknisi yang ditugaskan.
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_technician_id');
    }

    /**
     * Relasi ke riwayat pelaksanaan pemeliharaan.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class);
    }
}
