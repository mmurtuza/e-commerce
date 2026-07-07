<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Widgets\LatestOrdersTable;
use App\Filament\Widgets\LowStockAlert;
use App\Filament\Widgets\RevenueChart;
use App\Filament\Widgets\StatsOverview;
use App\Models\Setting;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = -10;

    public function getTitle(): string|Htmlable
    {
        return Setting::get('site_name', 'Dinajpur IT Park').' Dashboard';
    }

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            RevenueChart::class,
            LatestOrdersTable::class,
            LowStockAlert::class,
        ];
    }

    public function getColumns(): int|string|array
    {
        return [
            'default' => 1,
            'md' => 2,
            'xl' => 12,
        ];
    }
}
