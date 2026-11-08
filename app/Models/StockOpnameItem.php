<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StockOpnameResult;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpnameItem extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'stock_opname_id',
        'asset_id',
        'expected_stock',
        'actual_stock',
        'discrepancy_quantity',
        'discrepancy_value',
        'expected_location_id',
        'actual_location_id',
        'expected_condition',
        'actual_condition',
        'result',
        'status',
        'root_cause',
        'scanned_at',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expected_stock' => 'integer',
            'actual_stock' => 'integer',
            'discrepancy_quantity' => 'integer',
            'discrepancy_value' => 'decimal:2',
            'result' => StockOpnameResult::class,
            'scanned_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke sesi stock opname induk.
     */
    public function stockOpname(): BelongsTo
    {
        return $this->belongsTo(StockOpname::class);
    }

    /**
     * Relasi ke aset yang diaudit fisiknya.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Relasi ke lokasi/ruangan menurut data sistem.
     */
    public function expectedLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'expected_location_id');
    }

    /**
     * Relasi ke lokasi fisik tempat alat ditemukan saat audit.
     */
    public function actualLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'actual_location_id');
    }
}
