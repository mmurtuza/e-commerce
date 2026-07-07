<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Site Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Page Details')->schema([
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    Forms\Components\Tabs::make('Translations')->tabs([
                        Forms\Components\Tabs\Tab::make('English')->schema([
                            Forms\Components\TextInput::make('translations.en.title')
                                ->label('Title (EN)')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\RichEditor::make('translations.en.content')
                                ->label('Content (EN)')
                                ->required()
                                ->columnSpanFull(),
                        ]),
                        Forms\Components\Tabs\Tab::make('Bengali (বাংলা)')->schema([
                            Forms\Components\TextInput::make('translations.bn.title')
                                ->label('Title (BN)')
                                ->maxLength(255),
                            Forms\Components\RichEditor::make('translations.bn.content')
                                ->label('Content (BN)')
                                ->columnSpanFull(),
                        ]),
                    ])->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('translations.title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
