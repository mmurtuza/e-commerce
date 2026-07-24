<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\Setting;
use App\Services\SeoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoServiceTest extends TestCase
{
    use RefreshDatabase;

    private SeoService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SeoService;
    }

    private function createProduct(int $stockQuantity): Product
    {
        $category = Category::create([
            'slug' => 'cat-'.uniqid(),
            'is_active' => true,
        ]);

        return Product::create([
            'category_id' => $category->id,
            'slug' => 'product-'.uniqid(),
            'sku' => 'SKU-'.uniqid(),
            'price' => 150.00,
            'stock_quantity' => $stockQuantity,
            'is_active' => true,
        ]);
    }

    public function test_product_schema_for_in_stock_product(): void
    {
        $product = $this->createProduct(5);
        ProductTranslation::create([
            'product_id' => $product->id,
            'locale' => 'en',
            'name' => 'Test Product',
            'short_description' => 'Description',
        ]);

        $schema = $this->service->productSchema($product);

        $this->assertSame('https://schema.org', $schema['@context']);
        $this->assertSame('Product', $schema['@type']);
        $this->assertSame('Test Product', $schema['name']);
        $this->assertSame('https://schema.org/InStock', $schema['offers']['availability']);
    }

    public function test_product_schema_for_out_of_stock_product(): void
    {
        $product = $this->createProduct(0);
        $schema = $this->service->productSchema($product);

        $this->assertSame('https://schema.org/OutOfStock', $schema['offers']['availability']);
    }

    public function test_breadcrumb_schema(): void
    {
        $items = [
            ['name' => 'Home', 'url' => 'http://localhost'],
            ['name' => 'Shop', 'url' => 'http://localhost/shop'],
        ];

        $schema = $this->service->breadcrumbSchema($items);

        $this->assertSame('https://schema.org', $schema['@context']);
        $this->assertSame('BreadcrumbList', $schema['@type']);
        $this->assertCount(2, $schema['itemListElement']);
        $this->assertSame(1, $schema['itemListElement'][0]['position']);
        $this->assertSame('Home', $schema['itemListElement'][0]['name']);
    }

    public function test_organization_schema(): void
    {
        Setting::set('site_name', 'Test Store');
        Setting::set('meta_description', 'Test Description');

        $schema = $this->service->organizationSchema();

        $this->assertSame('https://schema.org', $schema['@context']);
        $this->assertSame('Organization', $schema['@type']);
        $this->assertSame('Test Store', $schema['name']);
        $this->assertSame('Test Description', $schema['description']);
    }

    public function test_get_meta_data(): void
    {
        Setting::set('site_name', 'Test Shop');
        Setting::set('site_tagline', 'Best Products');

        $meta = $this->service->getMetaData();

        $this->assertIsArray($meta);
        $this->assertSame('Test Shop', $meta['siteName']);
        $this->assertSame('Best Products', $meta['siteTagline']);
        $this->assertStringContainsString('Test Shop', $meta['title']);
    }
}
