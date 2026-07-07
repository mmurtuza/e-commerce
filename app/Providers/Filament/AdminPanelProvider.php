<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Pages\SetPassword;
use App\Filament\Pages\Settings;
use App\Filament\Resources\BannerResource;
use App\Filament\Resources\BlogResource;
use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\CouponResource;
use App\Filament\Resources\CustomerResource;
use App\Filament\Resources\OrderResource;
use App\Filament\Resources\ProductResource;
use App\Filament\Resources\ReviewResource;
use App\Filament\Widgets\AdminWelcome;
use App\Filament\Widgets\LatestOrdersTable;
use App\Filament\Widgets\LowStockAlert;
use App\Filament\Widgets\RevenueChart;
use App\Filament\Widgets\StatsOverview;
use App\Http\Middleware\RequireAdminPasswordChange;
use App\Http\Middleware\SetLocale;
use App\Models\Setting;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // Build panel path: "{ADMIN_PREFIX}/admin" or just "admin" when prefix is empty
        $prefix = env('ADMIN_PREFIX', '');
        $path = filled($prefix) ? trim($prefix, '/').'/admin' : 'admin';

        return $panel
            ->default()
            ->id('admin')
            ->path($path)
            ->login()
            ->colors(['primary' => Color::hex('#2D6A4F')])
            ->brandName(fn () => Setting::get('site_name', 'Dinajpur IT Park').' Admin')
            ->authGuard('admin')
            ->userMenuItems([
                MenuItem::make()
                    ->label('English')
                    ->icon('heroicon-o-language')
                    ->url('/language/en'),
                MenuItem::make()
                    ->label('বাংলা')
                    ->icon('heroicon-o-language')
                    ->url('/language/bn'),
            ])
            ->navigationGroups([
                NavigationGroup::make('Catalog'),
                NavigationGroup::make('Orders'),
                NavigationGroup::make('Marketing'),
                NavigationGroup::make('Content'),
                NavigationGroup::make('Users'),
                NavigationGroup::make('Settings'),
            ])
            ->resources([
                ProductResource::class,
                CategoryResource::class,
                OrderResource::class,
                CustomerResource::class,
                CouponResource::class,
                ReviewResource::class,
                BlogResource::class,
                BannerResource::class,
            ])
            ->pages([Dashboard::class, Settings::class, SetPassword::class])
            ->widgets([
                AdminWelcome::class,
                StatsOverview::class,
                RevenueChart::class,
                LatestOrdersTable::class,
                LowStockAlert::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                SetLocale::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                RequireAdminPasswordChange::class,
            ]);
    }
}
