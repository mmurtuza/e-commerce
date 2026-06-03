<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SetPassword extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.set-password';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Set Your Admin Password')
                    ->description('For security, you must set a new password before you can access the admin panel.')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('New Password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(8)
                            ->confirmed(),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Confirm Password')
                            ->password()
                            ->revealable()
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        /** @var \App\Models\Admin $admin */
        $admin = Auth::guard('admin')->user();

        $admin->update([
            'password'            => Hash::make($data['password']),
            'password_changed_at' => now(),
        ]);

        Notification::make()
            ->title('Password updated successfully. Welcome!')
            ->success()
            ->send();

        $this->redirect(filament()->getUrl());
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'Set Admin Password';
    }
}
