<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Models\Blog;
use App\Models\BlogCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Blog Content')->schema([
                Forms\Components\Tabs::make('Translations')->tabs([
                    Forms\Components\Tabs\Tab::make('English')->schema([
                        Forms\Components\TextInput::make(
                            'translations.en.title',
                        )
                            ->label('Title (English)')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (
                                string $operation,
                                ?string $state,
                                Forms\Set $set,
                            ): void {
                                if ($operation === 'create' && $state) {
                                    $set('slug', Str::slug($state));
                                }
                            }),
                        Forms\Components\Textarea::make(
                            'translations.en.excerpt',
                        )
                            ->label('Excerpt (English)')
                            ->rows(2)
                            ->maxLength(500),
                        Forms\Components\RichEditor::make(
                            'translations.en.content',
                        )
                            ->label('Content (English)')
                            ->columnSpanFull(),
                    ]),
                    Forms\Components\Tabs\Tab::make('বাংলা')->schema([
                        Forms\Components\TextInput::make(
                            'translations.bn.title',
                        )
                            ->label('শিরোনাম (বাংলা)')
                            ->maxLength(255),
                        Forms\Components\Textarea::make(
                            'translations.bn.excerpt',
                        )
                            ->label('সারসংক্ষেপ (বাংলা)')
                            ->rows(2)
                            ->maxLength(500),
                        Forms\Components\RichEditor::make(
                            'translations.bn.content',
                        )
                            ->label('বিষয়বস্তু (বাংলা)')
                            ->columnSpanFull(),
                    ]),
                ]),
            ]),

            Forms\Components\Section::make('Publishing Details')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(Blog::class, 'slug', ignoreRecord: true),
                    Forms\Components\Select::make('blog_category_id')
                        ->label('Category')
                        ->options(
                            BlogCategory::with('translations')
                                ->get()
                                ->pluck('name', 'id'),
                        )
                        ->searchable()
                        ->nullable(),
                ]),
                Forms\Components\FileUpload::make('featured_image')
                    ->label('Featured Image')
                    ->image()
                    ->directory('blog')
                    ->maxSize(4096)
                    ->imagePreviewHeight('150'),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Toggle::make('is_published')
                        ->label('Published')
                        ->default(false)
                        ->live(),
                    Forms\Components\DateTimePicker::make('published_at')
                        ->label('Publish At')
                        ->nullable()
                        ->visible(
                            fn (Forms\Get $get): bool => (bool) $get(
                                'is_published',
                            ),
                        ),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Image')
                    ->defaultImageUrl(asset('images/placeholder.png')),
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable(
                        query: function ($query, string $value): void {
                            $query->whereHas(
                                'translations',
                                fn ($q) => $q->where(
                                    'title',
                                    'like',
                                    "%{$value}%",
                                ),
                            );
                        },
                    )
                    ->limit(50)
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->default('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ToggleColumn::make('is_published')->label(
                    'Published',
                ),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Published At')
                    ->dateTime('d M Y')
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Published Status')
                    ->trueLabel('Published')
                    ->falseLabel('Draft'),
                Tables\Filters\SelectFilter::make('blog_category_id')
                    ->label('Category')
                    ->options(
                        BlogCategory::with('translations')
                            ->get()
                            ->pluck('name', 'id'),
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publish Selected')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->action(
                            fn ($records) => $records->each->update([
                                'is_published' => true,
                            ]),
                        ),
                    Tables\Actions\BulkAction::make('unpublish')
                        ->label('Unpublish Selected')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->action(
                            fn ($records) => $records->each->update([
                                'is_published' => false,
                            ]),
                        ),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['category', 'translations']);
    }
}
