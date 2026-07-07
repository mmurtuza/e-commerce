<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;

class ShippingService
{
    private array $zones = [
        'Dhaka' => ['base' => 60, 'per_kg' => 20],
        'Chittagong' => ['base' => 100, 'per_kg' => 30],
        'Rajshahi' => ['base' => 120, 'per_kg' => 35],
        'Khulna' => ['base' => 120, 'per_kg' => 35],
        'Sylhet' => ['base' => 130, 'per_kg' => 40],
        'Barisal' => ['base' => 130, 'per_kg' => 40],
        'Rangpur' => ['base' => 130, 'per_kg' => 40],
        'Mymensingh' => ['base' => 100, 'per_kg' => 30],
    ];

    public function calculate(string $division, int $weightGrams = 0): float
    {
        $zone = $this->zones[$division] ?? ['base' => 150, 'per_kg' => 50];
        $weightKg = $weightGrams / 1000;
        $extraWeight = max(0, $weightKg - 1);

        return $zone['base'] + ($extraWeight * $zone['per_kg']);
    }

    public function getEstimatedDays(string $division): int
    {
        return match ($division) {
            'Dhaka' => 1,
            'Chittagong', 'Mymensingh' => 2,
            default => 3,
        };
    }

    public function isFreeShipping(float $orderTotal): bool
    {
        return $orderTotal >= (float) Setting::get('free_shipping_threshold', '1500');
    }

    public function getDivisions(): array
    {
        return array_keys($this->zones);
    }
}
