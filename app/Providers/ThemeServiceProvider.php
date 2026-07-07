<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (! $this->app->runningInConsole() && ! request()->is('admin*')) {
            try {
                $layoutTheme = Setting::get('layout_theme', 'default');
                if ($layoutTheme !== 'default') {
                    View::prependLocation(resource_path("views/themes/{$layoutTheme}"));
                }
            } catch (\Exception $e) {
                // Settings table might not exist yet
            }
        }
    }
}
