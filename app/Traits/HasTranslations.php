<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Facades\App;

trait HasTranslations
{
    public function translate(?string $locale = null): ?object
    {
        $locale = $locale ?? App::getLocale();
        $relation = $this->translations ?? collect();

        return $relation->firstWhere('locale', $locale)
            ?? $relation->firstWhere('locale', config('app.fallback_locale', 'en'));
    }

    public function getTranslation(string $field, ?string $locale = null): ?string
    {
        return $this->translate($locale)?->{$field};
    }

    public function setTranslation(string $locale, array $data): void
    {
        $translationClass = $this->getTranslationModelClass();

        $this->translations()->updateOrCreate(
            ['locale' => $locale],
            $data
        );
    }

    protected function getTranslationModelClass(): string
    {
        return static::class.'Translation';
    }
}
