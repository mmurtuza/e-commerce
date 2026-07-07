<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\RepositoryServiceProvider;
use App\Providers\ThemeServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    RepositoryServiceProvider::class,
    ThemeServiceProvider::class,
];
