<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Support\DocumentNumberGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'asset_code',
        'name',
        'stock',
        'safety_stock',
        'brand',
        'model',
        'serial_number',
        'acquisition_date',
        'acquisition_cost',
        'funding_source',
        'warranty_expiry_date',
        'condition',
        'status',
        'is_critical',
        'photo_url',
        'notes',
        'category_id',
        'location_id',
        'department_id',
        'vendor_id',
        'created_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'condition' => AssetCondition::class,
            'status' => AssetStatus::class,
            'stock' => 'integer',
            'safety_stock' => 'integer',
            'is_critical' => 'boolean',
            'acquisition_date' => 'date',
            'warranty_expiry_date' => 'date',
            'acquisition_cost' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $asset): void {
            if (empty($asset->asset_code)) {
                $asset->asset_code = DocumentNumberGenerator::generateAssetCode();
            }
        });
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo<Location, $this>
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * @return BelongsTo<Department, $this>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * @return BelongsTo<Vendor, $this>
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return HasMany<AssetDocument, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(AssetDocument::class);
    }

    /**
     * @param  Builder<Asset>  $query
     * @return Builder<Asset>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', AssetStatus::AKTIF);
    }

    /**
     * @param  Builder<Asset>  $query
     * @return Builder<Asset>
     */
    public function scopeCritical(Builder $query): Builder
    {
        return $query->where('is_critical', true);
    }

    /**
     * @param  Builder<Asset>  $query
     * @return Builder<Asset>
     */
    public function scopeInDepartment(Builder $query, int $departmentId): Builder
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Teks informasi lengkap aset yang langsung terbaca saat QR code dipindai oleh kamera HP apa saja.
     */
    public function getQrScanText(): string
    {
        $cat = $this->category?->name ?? '-';
        $loc = $this->location?->name ?? '-';
        $dept = $this->department?->name ?? '-';
        $cond = $this->condition instanceof AssetCondition ? $this->condition->value : (string) $this->condition;
        $stat = $this->status instanceof AssetStatus ? $this->status->value : (string) $this->status;

        return "SIMAS-RS UMMI BOGOR\n"
            ."Kode: {$this->asset_code}\n"
            ."Nama: {$this->name}\n"
            ."Kategori: {$cat}\n"
            ."Lokasi: {$loc}\n"
            ."Unit: {$dept}\n"
            ."Kondisi: {$cond} | Status: {$stat}";
    }
}
