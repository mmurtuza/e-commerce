<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\SettingType;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Site Settings';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::all()->keyBy('key');

        $this->form->fill([
            'site_name' => $settings->get('site_name')?->value ?? '',
            'site_logo' => $settings->get('site_logo')?->value ?? '',
            'free_shipping_threshold' => $settings->get('free_shipping_threshold')?->value ?? '',
            'phone' => $settings->get('phone')?->value ?? '',
            'email' => $settings->get('email')?->value ?? '',
            'address' => $settings->get('address')?->value ?? '',
            'theme' => $settings->get('theme')?->value ?? 'default',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General')->schema([
                    Forms\Components\TextInput::make('site_name')->label('Site Name'),
                    Forms\Components\FileUpload::make('site_logo')->label('Site Logo')->image()->directory('settings'),
                    Forms\Components\TextInput::make('free_shipping_threshold')->label('Free Shipping Threshold (৳)')->numeric(),
                ])->columns(2),
                Forms\Components\Section::make('Contact')->schema([
                    Forms\Components\TextInput::make('phone')->label('Phone')->rule(new \App\Rules\BdPhone()),
                    Forms\Components\TextInput::make('email')->label('Email')->email(),
                    Forms\Components\Textarea::make('address')->label('Address')->columnSpanFull(),
                ])->columns(2),
                Forms\Components\Section::make('Appearance')->schema([
                    Forms\Components\Select::make('theme')
                        ->label('Site Theme')
                        ->options([
                            'default' => 'Forest Green',
                            'ocean' => 'Ocean Blue',
                            'sunset' => 'Sunset Orange',
                            'midnight' => 'Midnight Dark',
                            'rose' => 'Rose Gold',
                        ])
                        ->required(),
                ])->columns(1),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}
