<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Department;
use App\Models\User;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use UnitEnum;

class PrintAssetQrCodes extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQrCode;

    protected static UnitEnum|string|null $navigationGroup = 'Manajemen Aset';

    protected static ?string $navigationLabel = 'Cetak Label QR';

    protected static ?string $title = 'Cetak Batch QR Code Aset';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.print-asset-qr-codes';

    public string $activeTab = 'print'; // 'print' or 'scan'

    public string $search = '';

    public string $printMode = 'all'; // 'all' or 'selected'

    public string $sortBy = 'asset_code'; // 'asset_code', 'name', 'created_at'

    public string $sortDirection = 'asc'; // 'asc', 'desc'

    public string $layoutFormat = '3x5'; // '3x5', '2x4', 'square'

    public ?int $departmentId = null;

    public ?int $categoryId = null;

    public string $scanInput = '';

    public ?Asset $scannedAsset = null;

    /**
     * @var array<int>
     */
    public array $selectedAssetIds = [];

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = auth()->user();

        return $user !== null && ($user->isAdmin() || $user->isAssetStaff());
    }

    public function mount(): void
    {
        $this->selectedAssetIds = $this->getBaseQuery()->pluck('id')->toArray();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = in_array($tab, ['print', 'scan'], true) ? $tab : 'print';
    }

    public function selectAll(): void
    {
        $this->selectedAssetIds = $this->getBaseQuery()->pluck('id')->toArray();
    }

    public function deselectAll(): void
    {
        $this->selectedAssetIds = [];
    }

    public function processScan(): void
    {
        $raw = trim($this->scanInput);
        if ($raw === '') {
            return;
        }

        // Ekstraksi kode aset jika yang dipindai adalah format teks lengkap
        if (preg_match('/(?:Kode:\s*)?(AST-\d{4}-\d+)/i', $raw, $matches)) {
            $code = $matches[1];
        } else {
            $code = $raw;
        }

        $asset = Asset::with(['category', 'department', 'location', 'vendor', 'creator'])
            ->where('asset_code', $code)
            ->orWhere('name', 'like', "%{$code}%")
            ->first();

        if ($asset !== null) {
            $this->scannedAsset = $asset;
            $this->scanInput = $asset->asset_code;
            Notification::make()
                ->title('Aset Ditemukan')
                ->body("{$asset->asset_code} - {$asset->name}")
                ->success()
                ->send();
        } else {
            $this->scannedAsset = null;
            Notification::make()
                ->title('Aset Tidak Ditemukan')
                ->body("Kode aset '{$code}' tidak terdaftar di sistem.")
                ->danger()
                ->send();
        }
    }

    public function clearScan(): void
    {
        $this->scanInput = '';
        $this->scannedAsset = null;
    }

    /**
     * @return Builder<Asset>
     */
    public function getBaseQuery(): Builder
    {
        $query = Asset::query()->with(['category', 'department', 'location']);

        if ($this->search !== '') {
            $query->where(function (Builder $q): void {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('asset_code', 'like', "%{$this->search}%")
                    ->orWhere('brand', 'like', "%{$this->search}%")
                    ->orWhere('model', 'like', "%{$this->search}%");
            });
        }

        if ($this->departmentId !== null) {
            $query->where('department_id', $this->departmentId);
        }

        if ($this->categoryId !== null) {
            $query->where('category_id', $this->categoryId);
        }

        $allowedSorts = ['asset_code', 'name', 'created_at', 'category_id'];
        $sortColumn = in_array($this->sortBy, $allowedSorts, true) ? $this->sortBy : 'asset_code';
        $direction = strtolower($this->sortDirection) === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($sortColumn, $direction);
    }

    /**
     * @return Collection<int, Asset>
     */
    public function getAssets(): Collection
    {
        $query = $this->getBaseQuery();

        if ($this->printMode === 'selected') {
            if (empty($this->selectedAssetIds)) {
                return new Collection;
            }
            $query->whereIn('id', $this->selectedAssetIds);
        }

        return $query->get();
    }

    public function getTotalAssetsCount(): int
    {
        return $this->getAssets()->count();
    }

    public function getItemsPerA4Page(): int
    {
        return match ($this->layoutFormat) {
            '4x6' => 24,
            'square' => 12,
            default => 15, // 3x5
        };
    }

    public function getEstimatedA4Pages(): int
    {
        $total = $this->getTotalAssetsCount();
        if ($total <= 0) {
            return 0;
        }

        return (int) ceil($total / $this->getItemsPerA4Page());
    }

    /**
     * @return Collection<int, Department>
     */
    public function getDepartments(): Collection
    {
        return Department::orderBy('name')->get();
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return Category::orderBy('name')->get();
    }
}
