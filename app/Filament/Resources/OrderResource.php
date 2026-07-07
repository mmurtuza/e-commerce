<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Order Information')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('order_number')
                        ->label('Order Number')
                        ->disabled(),
                    Forms\Components\TextInput::make('user.name')
                        ->label('Customer')
                        ->disabled(),
                ]),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Select::make('status')
                        ->label('Order Status')
                        ->options(OrderStatus::options())
                        ->required(),
                    Forms\Components\Select::make('payment_status')
                        ->label('Payment Status')
                        ->options(PaymentStatus::options())
                        ->disabled(),
                ]),
            ]),

            Forms\Components\Section::make('Financials')->schema([
                Forms\Components\Grid::make(4)->schema([
                    Forms\Components\TextInput::make('subtotal')
                        ->label('Subtotal (৳)')
                        ->prefix('৳')
                        ->disabled(),
                    Forms\Components\TextInput::make('discount_amount')
                        ->label('Discount (৳)')
                        ->prefix('৳')
                        ->disabled(),
                    Forms\Components\TextInput::make('shipping_amount')
                        ->label('Shipping (৳)')
                        ->prefix('৳')
                        ->disabled(),
                    Forms\Components\TextInput::make('total')
                        ->label('Total (৳)')
                        ->prefix('৳')
                        ->disabled(),
                ]),
            ]),

            Forms\Components\Section::make('Notes')->schema([
                Forms\Components\Textarea::make('notes')
                    ->label('Order Notes')
                    ->rows(3)
                    ->disabled(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('BDT')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (OrderStatus $state): string => $state->label())
                    ->color(fn (OrderStatus $state): string => $state->color()),
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('Payment')
                    ->formatStateUsing(fn (PaymentStatus $state): string => $state->label())
                    ->color(fn (PaymentStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Placed At')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Order Status')
                    ->options(OrderStatus::options()),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Payment Status')
                    ->options(PaymentStatus::options()),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From'),
                        Forms\Components\DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('From '.$data['from'])->removeField('from');
                        }
                        if ($data['until'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Until '.$data['until'])->removeField('until');
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Manage'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_confirmed')
                        ->label('Mark as Confirmed')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['status' => OrderStatus::Confirmed]))
                        ->requiresConfirmation(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ShipmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user']);
    }
}
