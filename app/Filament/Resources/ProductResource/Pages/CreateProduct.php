<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['translations']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->getRecord();
        $translations = $this->data['translations'] ?? [];

        foreach ($translations as $locale => $translationData) {
            $record->setTranslation($locale, $translationData);
        }
    }
}
