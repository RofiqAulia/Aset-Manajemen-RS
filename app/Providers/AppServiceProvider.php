<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Category;
use App\Models\Department;
use App\Models\Location;
use App\Models\User;
use App\Models\Vendor;
use App\Policies\MasterDataPolicy;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Category::class, MasterDataPolicy::class);
        Gate::policy(Location::class, MasterDataPolicy::class);
        Gate::policy(Department::class, MasterDataPolicy::class);
        Gate::policy(Vendor::class, MasterDataPolicy::class);

        Gate::before(function (User $user, string $ability): ?bool {
            if ($user->isAdmin()) {
                return true;
            }

            return null;
        });

        Table::configureUsing(function (Table $table): void {
            $table
                ->paginationMode(PaginationMode::Default)
                ->paginationPageOptions([5, 10, 25, 50])
                ->defaultPaginationPageOption(5)
                ->extremePaginationLinks();
        }, isImportant: true);
    }
}
