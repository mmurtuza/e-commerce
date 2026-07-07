<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\CouponType;
use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Coupon Details')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('code')
                        ->label('Coupon Code')
                        ->required()
                        ->maxLength(50)
                        ->unique(Coupon::class, 'code', ignoreRecord: true)
                        ->placeholder('e.g. SUMMER20')
                        ->helperText(
                            'Customers will enter this code at checkout.',
                        ),
                    Forms\Components\Select::make('type')
                        ->label('Discount Type')
                        ->options(CouponType::options())
                        ->required()
                        ->live(),
                ]),
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\TextInput::make('value')
                        ->label(
                            fn (Forms\Get $get): string => $get('type') ===
                            CouponType::Percentage->value
                                ? 'Discount (%)'
                                : 'Discount Amount (৳)',
                        )
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->suffix(
                            fn (Forms\Get $get): string => $get('type') ===
                            CouponType::Percentage->value
                                ? '%'
                                : '৳',
                        ),
                    Forms\Components\TextInput::make('min_order_amount')
                        ->label('Min. Order Amount (৳)')
                        ->numeric()
                        ->prefix('৳')
                        ->nullable(),
                    Forms\Components\TextInput::make('max_discount_amount')
                        ->label('Max. Discount Amount (৳)')
                        ->numeric()
                        ->prefix('৳')
                        ->nullable()
                        ->helperText('Cap for percentage-based coupons.'),
                ]),
            ]),

            Forms\Components\Section::make('Usage & Validity')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('usage_limit')
                        ->label('Usage Limit')
                        ->numeric()
                        ->nullable()
                        ->helperText('Leave blank for unlimited uses.'),
                    Forms\Components\TextInput::make('used_count')
                        ->label('Times Used')
                        ->numeric()
                        ->disabled()
                        ->default(0),
                ]),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\DateTimePicker::make('starts_at')
                        ->label('Starts At')
                        ->nullable(),
                    Forms\Components\DateTimePicker::make('expires_at')
                        ->label('Expires At')
                        ->nullable()
                        ->after('starts_at'),
                ]),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(
                        fn (CouponType $state): string => $state->label(),
                    )
                    ->color(fn (CouponType $state): string => $state->color()),
                Tables\Columns\TextColumn::make('value')
                    ->label('Value')
                    ->formatStateUsing(function (Coupon $record): string {
                        return $record->type === CouponType::Percentage
                            ? $record->value.'%'
                            : '৳'.number_format((float) $record->value, 2);
                    }),
                Tables\Columns\TextColumn::make('usage')
                    ->label('Used / Limit')
                    ->state(
                        fn (Coupon $record): string => $record->used_count.
                            ' / '.
                            ($record->usage_limit ?? '∞'),
                    ),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime('d M Y')
                    ->placeholder('Never')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')->label('Active'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label(
                    'Active Status',
                ),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Discount Type')
                    ->options(CouponType::options()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
