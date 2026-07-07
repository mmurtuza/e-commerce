<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\User;
use App\Rules\BdPhone;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomerResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Customer';

    protected static ?string $pluralModelLabel = 'Customers';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Customer Information')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Full Name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->label('Email Address')
                        ->email()
                        ->required()
                        ->maxLength(255),
                ]),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('phone')
                        ->label('Phone Number')
                        ->tel()
                        ->rule(new BdPhone)
                        ->maxLength(20),
                    Forms\Components\Select::make('locale')
                        ->label('Preferred Language')
                        ->options(['en' => 'English', 'bn' => 'বাংলা'])
                        ->default('en'),
                ]),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active Account')
                    ->default(true),
            ]),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Customer Details')->schema([
                Infolists\Components\Grid::make(2)->schema([
                    Infolists\Components\TextEntry::make('name')->label(
                        'Full Name',
                    ),
                    Infolists\Components\TextEntry::make('email')->label(
                        'Email',
                    ),
                    Infolists\Components\TextEntry::make('phone')
                        ->label('Phone')
                        ->default('N/A'),
                    Infolists\Components\TextEntry::make('locale')
                        ->label('Language')
                        ->formatStateUsing(
                            fn (?string $state): string => match ($state) {
                                'bn' => 'বাংলা',
                                default => 'English',
                            },
                        ),
                    Infolists\Components\TextEntry::make('created_at')
                        ->label('Member Since')
                        ->dateTime('d M Y'),
                    Infolists\Components\IconEntry::make('is_active')
                        ->label('Active')
                        ->boolean(),
                ]),
            ]),

            Infolists\Components\Section::make('Order Summary')->schema([
                Infolists\Components\Grid::make(3)->schema([
                    Infolists\Components\TextEntry::make('orders_count')
                        ->label('Total Orders')
                        ->state(
                            fn (User $record): int => $record->orders()->count(),
                        ),
                    Infolists\Components\TextEntry::make('total_spent')
                        ->label('Total Spent')
                        ->state(
                            fn (User $record): string => '৳'.
                                number_format($record->total_spent, 2),
                        ),
                    Infolists\Components\TextEntry::make('email_verified_at')
                        ->label('Email Verified')
                        ->dateTime('d M Y')
                        ->placeholder('Not Verified'),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->default('—')
                    ->searchable(),
                Tables\Columns\TextColumn::make('orders_count')
                    ->label('Orders')
                    ->counts('orders')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('Verified')
                    ->dateTime('d M Y')
                    ->placeholder('No')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Account Status')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive'),
                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label('Email Verified')
                    ->nullable()
                    ->trueLabel('Verified')
                    ->falseLabel('Unverified'),
            ])
            ->actions([Tables\Actions\ViewAction::make()])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'view' => Pages\ViewCustomer::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount('orders');
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
