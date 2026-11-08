<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\DocumentNumberGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockOpname extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'opname_number',
        'title',
        'location_id',
        'department_id',
        'start_date',
        'end_date',
        'auditor_id',
        'status',
        'total_expected',
        'total_scanned',
        'total_discrepancy',
        'total_variance_value',
        'total_accuracy_rate',
        'physical_progress_rate',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'total_expected' => 'integer',
            'total_scanned' => 'integer',
            'total_discrepancy' => 'integer',
            'total_variance_value' => 'decimal:2',
            'total_accuracy_rate' => 'decimal:2',
            'physical_progress_rate' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $opname): void {
            if (empty($opname->opname_number)) {
                $opname->opname_number = DocumentNumberGenerator::generateStockOpnameNumber();
            }
        });

        static::created(function (self $opname): void {
            if ($opname->items()->count() === 0) {
                $opname->populateItems();
            }
        });
    }

    /**
     * Otomatis memuat daftar item aset yang harus dihitung fisik.
     */
    public function populateItems(): void
    {
        $query = Asset::query();

        if ($this->location_id) {
            $query->where('location_id', $this->location_id);
        }

        if ($this->department_id) {
            $query->where('department_id', $this->department_id);
        }

        $assets = $query->get();

        foreach ($assets as $asset) {
            $this->items()->create([
                'asset_id' => $asset->id,
                'expected_stock' => (int) ($asset->stock ?? 1),
                'actual_stock' => null,
                'discrepancy_quantity' => 0,
                'discrepancy_value' => 0,
                'expected_location_id' => $asset->location_id,
                'actual_location_id' => null,
                'expected_condition' => $asset->condition?->value ?? 'baik',
                'actual_condition' => null,
                'status' => 'belum_diperiksa',
                'result' => 'ditemukan',
            ]);
        }

        $this->recalculateMetrics();
    }

    /**
     * Menghitung ulang seluruh metrik ringkasan stock opname.
     */
    public function recalculateMetrics(): void
    {
        $totalItems = $this->items()->count();
        $scannedItems = $this->items()->whereNotNull('actual_stock')->count();
        $discrepantItems = $this->items()->where('status', 'selisih')->count();
        $totalVarianceValue = (float) $this->items()->sum('discrepancy_value');

        $progressRate = $totalItems > 0 ? round(($scannedItems / $totalItems) * 100, 2) : 0.00;
        $accuracyRate = $scannedItems > 0 ? round((($scannedItems - $discrepantItems) / $scannedItems) * 100, 2) : 100.00;

        $this->updateQuietly([
            'total_expected' => $totalItems,
            'total_scanned' => $scannedItems,
            'total_discrepancy' => $discrepantItems,
            'total_variance_value' => $totalVarianceValue,
            'total_accuracy_rate' => $accuracyRate,
            'physical_progress_rate' => $progressRate,
        ]);
    }

    /**
     * Relasi ke ruangan yang diaudit (opsional, jika audit per ruangan spesifik).
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Relasi ke unit penanggung jawab yang diaudit.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Relasi ke petugas / auditor internal yang memimpin stock opname.
     */
    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    /**
     * Relasi ke rincian item aset yang diaudit.
     */
    public function items(): HasMany
    {
        return $this->hasMany(StockOpnameItem::class);
    }

    /**
     * Generator nomor kegiatan stock opname terstandarisasi (SO-YYYYMM-NNNN).
     */
    public static function generateOpnameNumber(): string
    {
        $prefix = 'SO-'.date('Ym').'-';
        $latest = static::withTrashed()
            ->where('opname_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->first();

        if (! $latest) {
            return $prefix.'0001';
        }

        $lastSeq = (int) substr($latest->opname_number, -4);
        $nextSeq = str_pad((string) ($lastSeq + 1), 4, '0', STR_PAD_LEFT);

        return $prefix.$nextSeq;
    }
}
