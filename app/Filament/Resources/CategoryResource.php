<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Category Details')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('translations.en.name')
                        ->label('Name (English)')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $operation, ?string $state, Forms\Set $set): void {
                            if ($operation === 'create' && $state) {
                                $set('slug', Str::slug($state));
                            }
                        }),
                    Forms\Components\TextInput::make('translations.bn.name')
                        ->label('Name (বাংলা)')
                        ->maxLength(255),
                ]),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(Category::class, 'slug', ignoreRecord: true),
                Forms\Components\Select::make('parent_id')
                    ->label('Parent Category')
                    ->options(
                        Category::with('translations')
                            ->whereNull('parent_id')
                            ->get()
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->nullable()
                    ->placeholder('None (Top Level)'),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('icon')
                        ->label('Icon Class')
                        ->maxLength(100)
                        ->placeholder('heroicon-o-tag'),
                    Forms\Components\TextInput::make('sort_order')
                        ->label('Sort Order')
                        ->numeric()
                        ->default(0),
                ]),
                Forms\Components\FileUpload::make('image')
                    ->label('Category Image')
                    ->disk('public')
                    ->image()
                    ->directory('categories')
                    ->maxSize(2048)
                    ->imagePreviewHeight('100'),
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
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl('/images/placeholder.png'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->formatStateUsing(function ($state, $record): string {
                        return $record->parent_id ? '↳ '.$state : $state;
                    })
                    ->searchable(query: function ($query, string $value): void {
                        $query->whereHas('translations', fn ($q) => $q->where('name', 'like', "%{$value}%"));
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Parent Category')
                    ->placeholder('— (Top Level)')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('children_count')
                    ->label('Subcategories')
                    ->counts('children')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'gray'),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active'),
            ])
            ->defaultSort('sort_order')
            ->groups([
                Tables\Grouping\Group::make('parent.name')
                    ->label('Parent Category')
                    ->collapsible(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->trueLabel('Active Only')
                    ->falseLabel('Inactive Only'),
                Tables\Filters\Filter::make('root_only')
                    ->label('Top-Level Only')
                    ->query(fn ($query) => $query->whereNull('parent_id')),
                Tables\Filters\Filter::make('sub_only')
                    ->label('Subcategories Only')
                    ->query(fn ($query) => $query->whereNotNull('parent_id')),
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Under Parent')
                    ->options(
                        Category::with('translations')
                            ->whereNull('parent_id')
                            ->get()
                            ->pluck('name', 'id')
                    ),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
