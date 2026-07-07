<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\DifficultyLevel;
use App\Enums\PlantType;
use App\Enums\SunlightRequirement;
use App\Enums\WateringFrequency;
use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Product')->tabs([

                Forms\Components\Tabs\Tab::make('General')->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('translations.en.name')
                            ->label('Name (English)')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('translations.bn.name')
                            ->label('Name (বাংলা)')
                            ->maxLength(255),
                    ]),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Select::make('parent_category_id')
                            ->label('Parent Category')
                            ->options(fn () => Category::whereNull('parent_id')->with('translations')->get()->pluck('name', 'id'))
                            ->live()
                            ->dehydrated(false)
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('category_id', null))
                            ->afterStateHydrated(function (Forms\Components\Select $component, ?Product $record) {
                                if ($record && $record->category) {
                                    $component->state($record->category->parent_id ?? $record->category_id);
                                }
                            })
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('category_id')
                            ->label('Category / Subcategory')
                            ->options(function (Forms\Get $get) {
                                $parentId = $get('parent_category_id');
                                if (! $parentId) {
                                    return Category::with('translations')->get()->pluck('name', 'id');
                                }

                                return Category::where('parent_id', $parentId)
                                    ->orWhere('id', $parentId)
                                    ->with('translations')
                                    ->get()
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('brand_id')
                            ->label('Brand')
                            ->relationship('brand', 'slug')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('tags')
                            ->multiple()
                            ->relationship('tags', 'name')
                            ->preload()
                            ->searchable(),
                    ]),
                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('translations.en.short_description')
                        ->label('Short Description (EN)')
                        ->rows(2),
                    Forms\Components\Textarea::make('translations.bn.short_description')
                        ->label('Short Description (BN)')
                        ->rows(2),
                    Forms\Components\RichEditor::make('translations.en.description')
                        ->label('Description (EN)')
                        ->columnSpanFull(),
                    Forms\Components\RichEditor::make('translations.bn.description')
                        ->label('Description (BN)')
                        ->columnSpanFull(),
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\Toggle::make('is_active')->label('Active')->default(true),
                        Forms\Components\Toggle::make('is_featured')->label('Featured'),
                        Forms\Components\Toggle::make('is_new_arrival')->label('New Arrival'),
                    ]),
                ]),

                Forms\Components\Tabs\Tab::make('Pricing & Stock')->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('price')->label('Price (৳)')->numeric()->required()->prefix('৳'),
                        Forms\Components\TextInput::make('compare_price')->label('Compare Price (৳)')->numeric()->prefix('৳'),
                        Forms\Components\TextInput::make('cost_price')->label('Cost Price (৳)')->numeric()->prefix('৳'),
                    ]),
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('sku')->label('SKU')->required(),
                        Forms\Components\TextInput::make('stock_quantity')->label('Stock Qty')->numeric()->required()->default(0),
                        Forms\Components\TextInput::make('low_stock_threshold')->label('Low Stock Alert')->numeric()->default(5),
                    ]),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('weight_grams')->label('Weight (grams)')->numeric(),
                        Forms\Components\TextInput::make('tax_rate')->label('Tax Rate (%)')->numeric()->default(0),
                    ]),
                ]),

                Forms\Components\Tabs\Tab::make('Plant Info')->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Select::make('plant_type')->options(PlantType::options()),
                        Forms\Components\Select::make('sunlight')->options(SunlightRequirement::options()),
                        Forms\Components\Select::make('watering')->options(WateringFrequency::options()),
                        Forms\Components\Select::make('difficulty')->options(DifficultyLevel::options()),
                        Forms\Components\TextInput::make('mature_size')->label('Mature Size'),
                    ]),
                    Forms\Components\Textarea::make('translations.en.care_instructions')->label('Care Instructions (EN)')->rows(4),
                    Forms\Components\Textarea::make('translations.bn.care_instructions')->label('Care Instructions (BN)')->rows(4),
                ]),

                Forms\Components\Tabs\Tab::make('SEO')->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('translations.en.meta_title')->label('Meta Title (EN)'),
                        Forms\Components\TextInput::make('translations.bn.meta_title')->label('Meta Title (BN)'),
                    ]),
                    Forms\Components\Textarea::make('translations.en.meta_description')->label('Meta Description (EN)')->rows(2),
                    Forms\Components\Textarea::make('translations.bn.meta_description')->label('Meta Description (BN)')->rows(2),
                ]),

            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('primaryImage.path')->label('Image')->disk('public')->circular(false)->size(50),
                Tables\Columns\TextColumn::make('translations.name')->label('Name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('sku')->label('SKU')->searchable(),
                Tables\Columns\TextColumn::make('price')->label('Price')->money('BDT')->sortable(),
                Tables\Columns\TextColumn::make('stock_quantity')->label('Stock')
                    ->badge()
                    ->color(fn ($record) => $record->stock_quantity <= 0 ? 'danger' : ($record->stock_quantity <= $record->low_stock_threshold ? 'warning' : 'success')),
                Tables\Columns\TextColumn::make('category.name')->label('Category'),
                Tables\Columns\TextColumn::make('brand.name')->label('Brand'),
                Tables\Columns\ToggleColumn::make('is_active')->label('Active'),
                Tables\Columns\ToggleColumn::make('is_featured')->label('Featured'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')->relationship('category', 'id'),
                Tables\Filters\SelectFilter::make('plant_type')->options(PlantType::options()),
                Tables\Filters\TrashedFilter::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
