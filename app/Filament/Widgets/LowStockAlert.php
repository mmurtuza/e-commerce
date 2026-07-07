<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockAlert extends BaseWidget
{
    protected static ?string $heading = 'Low Stock Alert';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->with(['translations', 'images'])
                    ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                    ->where('is_active', true)
                    ->orderBy('stock_quantity')
            )
            ->columns([
                Tables\Columns\ImageColumn::make('primary_image.image')
                    ->label('Image')
                    ->circular()
                    ->defaultImageUrl(asset('images/placeholder.png')),
                Tables\Columns\TextColumn::make('name')
                    ->label('Product')
                    ->searchable(query: function ($query, string $value): void {
                        $query->whereHas('translations', fn ($q) => $q->where('name', 'like', "%{$value}%"));
                    })
                    ->limit(40),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('In Stock')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state <= 5 => 'warning',
                        default => 'primary',
                    }),
                Tables\Columns\TextColumn::make('low_stock_threshold')
                    ->label('Threshold')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('BDT'),
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->label('Update Stock')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn (Product $record): string => ProductResource::getUrl('edit', ['record' => $record])),
            ])
            ->emptyStateHeading('All products are well-stocked!')
            ->emptyStateDescription('No products are currently below their low-stock threshold.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->paginated([10, 25, 50]);
    }
}
