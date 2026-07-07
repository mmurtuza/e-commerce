<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\BannerType;
use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Banner Details')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Select::make('type')
                        ->label('Banner Type')
                        ->options(BannerType::options())
                        ->required(),
                    Forms\Components\TextInput::make('sort_order')
                        ->label('Sort Order')
                        ->numeric()
                        ->default(0),
                ]),
                Forms\Components\Tabs::make('Translations')->tabs([
                    Forms\Components\Tabs\Tab::make('English')->schema([
                        Forms\Components\TextInput::make('translations.en.title')
                            ->label('Title (English)')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('translations.en.subtitle')
                            ->label('Subtitle (English)')
                            ->rows(2),
                    ]),
                    Forms\Components\Tabs\Tab::make('বাংলা')->schema([
                        Forms\Components\TextInput::make('translations.bn.title')
                            ->label('শিরোনাম (বাংলা)')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('translations.bn.subtitle')
                            ->label('সাবটাইটেল (বাংলা)')
                            ->rows(2),
                    ]),
                ]),
                Forms\Components\TextInput::make('link')
                    ->label('Link URL')
                    ->url()
                    ->nullable()
                    ->placeholder('https://example.com/page'),
            ]),

            Forms\Components\Section::make('Images')->schema([
                Forms\Components\FileUpload::make('image')
                    ->label('Desktop Image')
                    ->image()
                    ->directory('banners')
                    ->required()
                    ->maxSize(5120)
                    ->imagePreviewHeight('150'),
                Forms\Components\FileUpload::make('mobile_image')
                    ->label('Mobile Image')
                    ->image()
                    ->directory('banners/mobile')
                    ->nullable()
                    ->maxSize(3072)
                    ->imagePreviewHeight('100')
                    ->helperText('Optional: upload a cropped version for mobile screens.'),
            ]),

            Forms\Components\Section::make('Schedule & Status')->schema([
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
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->height(60)
                    ->extraImgAttributes(['class' => 'rounded']),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn (BannerType $state): string => $state->label())
                    ->color(fn (BannerType $state): string => $state->color()),
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->default('—')
                    ->limit(40),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Starts')
                    ->dateTime('d M Y')
                    ->placeholder('Immediate')
                    ->sortable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime('d M Y')
                    ->placeholder('Never')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Banner Type')
                    ->options(BannerType::options()),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
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
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
