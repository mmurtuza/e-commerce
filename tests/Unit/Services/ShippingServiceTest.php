<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Setting;
use App\Services\ShippingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingServiceTest extends TestCase
{
    use RefreshDatabase;

    private ShippingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ShippingService;
    }

    public function test_calculate_shipping_cost_for_known_divisions_within_base_weight(): void
    {
        $this->assertEquals(60.0, $this->service->calculate('Dhaka', 500));
        $this->assertEquals(100.0, $this->service->calculate('Chittagong', 1000));
        $this->assertEquals(120.0, $this->service->calculate('Rajshahi', 800));
        $this->assertEquals(120.0, $this->service->calculate('Khulna', 900));
        $this->assertEquals(130.0, $this->service->calculate('Sylhet', 1000));
        $this->assertEquals(130.0, $this->service->calculate('Barisal', 750));
        $this->assertEquals(130.0, $this->service->calculate('Rangpur', 500));
        $this->assertEquals(100.0, $this->service->calculate('Mymensingh', 600));
    }

    public function test_calculate_shipping_cost_with_extra_weight(): void
    {
        // Dhaka: base 60, per_kg 20. 3000g = 3kg, extra weight = 2kg => 60 + (2 * 20) = 100
        $this->assertEquals(100.0, $this->service->calculate('Dhaka', 3000));
    }

    public function test_calculate_shipping_cost_for_unknown_division_uses_default_rates(): void
    {
        // Default: base 150, per_kg 50. 2000g = 2kg, extra weight = 1kg => 150 + (1 * 50) = 200
        $this->assertEquals(200.0, $this->service->calculate('UnknownDivision', 2000));
    }

    public function test_get_estimated_days(): void
    {
        $this->assertSame(1, $this->service->getEstimatedDays('Dhaka'));
        $this->assertSame(2, $this->service->getEstimatedDays('Chittagong'));
        $this->assertSame(2, $this->service->getEstimatedDays('Mymensingh'));
        $this->assertSame(3, $this->service->getEstimatedDays('Rajshahi'));
        $this->assertSame(3, $this->service->getEstimatedDays('Unknown'));
    }

    public function test_is_free_shipping_with_default_and_custom_threshold(): void
    {
        $this->assertTrue($this->service->isFreeShipping(1500.0));
        $this->assertFalse($this->service->isFreeShipping(1499.0));

        Setting::set('free_shipping_threshold', '2000');
        $this->assertTrue($this->service->isFreeShipping(2000.0));
        $this->assertFalse($this->service->isFreeShipping(1999.0));
    }

    public function test_get_divisions(): void
    {
        $divisions = $this->service->getDivisions();
        $this->assertCount(8, $divisions);
        $this->assertContains('Dhaka', $divisions);
        $this->assertContains('Chittagong', $divisions);
    }
}
