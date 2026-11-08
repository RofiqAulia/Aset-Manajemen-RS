<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\WorkOrderPriority;
use App\Enums\WorkOrderStatus;
use App\Support\DocumentNumberGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrder extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'wo_number',
        'asset_id',
        'requester_id',
        'technician_id',
        'priority',
        'status',
        'description',
        'action_taken',
        'cost',
        'completion_notes',
        'completed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'priority' => WorkOrderPriority::class,
            'status' => WorkOrderStatus::class,
            'cost' => 'decimal:2',
            'completed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $workOrder): void {
            if (empty($workOrder->wo_number)) {
                $workOrder->wo_number = DocumentNumberGenerator::generateWorkOrderNumber();
            }
        });
    }

    /**
     * Relasi ke aset yang mengalami kerusakan/kendala.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Relasi ke staf/pengguna yang melaporkan tiket.
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /**
     * Relasi ke teknisi IPSRS yang ditugaskan.
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    /**
     * Generator nomor tiket Work Order terstandarisasi RS UMMI (WO-YYYYMM-NNNN).
     */
    public static function generateWoNumber(): string
    {
        $prefix = 'WO-'.date('Ym').'-';
        $latest = static::withTrashed()
            ->where('wo_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->first();

        if (! $latest) {
            return $prefix.'0001';
        }

        $lastSeq = (int) substr($latest->wo_number, -4);
        $nextSeq = str_pad((string) ($lastSeq + 1), 4, '0', STR_PAD_LEFT);

        return $prefix.$nextSeq;
    }
}
