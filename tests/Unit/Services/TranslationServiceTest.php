<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Services\TranslationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class TranslationServiceTest extends TestCase
{
    use RefreshDatabase;

    private TranslationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TranslationService;
    }

    private function createProduct(): Product
    {
        $category = Category::create([
            'slug' => 'cat-'.uniqid(),
            'is_active' => true,
        ]);

        return Product::create([
            'category_id' => $category->id,
            'slug' => 'product-'.uniqid(),
            'sku' => 'SKU-'.uniqid(),
            'price' => 100.00,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);
    }

    public function test_get_available_locales(): void
    {
        $locales = $this->service->getAvailableLocales();
        $this->assertSame(['bn' => 'বাংলা', 'en' => 'English'], $locales);
    }

    public function test_get_returns_translation_for_current_locale(): void
    {
        $product = $this->createProduct();
        ProductTranslation::create([
            'product_id' => $product->id,
            'locale' => 'bn',
            'name' => 'বাংলা নাম',
        ]);

        App::setLocale('bn');
        $this->assertSame('বাংলা নাম', $this->service->get($product, 'name'));
    }

    public function test_get_falls_back_to_fallback_locale(): void
    {
        $product = $this->createProduct();
        ProductTranslation::create([
            'product_id' => $product->id,
            'locale' => 'en',
            'name' => 'English Name',
        ]);

        App::setLocale('fr');
        $this->assertSame('English Name', $this->service->get($product, 'name'));
    }

    public function test_get_returns_null_when_no_translation_exists(): void
    {
        $product = $this->createProduct();
        $this->assertNull($this->service->get($product, 'name', 'es'));
    }

    public function test_set_creates_or_updates_translation(): void
    {
        $product = $this->createProduct();

        $this->service->set($product, 'bn', ['name' => 'নতুন নাম']);
        $this->assertDatabaseHas('product_translations', [
            'product_id' => $product->id,
            'locale' => 'bn',
            'name' => 'নতুন নাম',
        ]);

        $this->service->set($product, 'bn', ['name' => 'আপডেটেড নাম']);
        $this->assertDatabaseHas('product_translations', [
            'product_id' => $product->id,
            'locale' => 'bn',
            'name' => 'আপডেটেড নাম',
        ]);
    }
}
