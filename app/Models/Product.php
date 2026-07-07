<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DifficultyLevel;
use App\Enums\PlantType;
use App\Enums\SunlightRequirement;
use App\Enums\WateringFrequency;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable = [
        'category_id', 'brand_id', 'slug', 'sku', 'barcode', 'price', 'compare_price',
        'cost_price', 'stock_quantity', 'low_stock_threshold', 'weight_grams',
        'is_active', 'is_featured', 'is_new_arrival', 'requires_shipping',
        'tax_rate', 'plant_type', 'sunlight', 'watering', 'difficulty', 'mature_size',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_new_arrival' => 'boolean',
            'requires_shipping' => 'boolean',
            'plant_type' => PlantType::class,
            'sunlight' => SunlightRequirement::class,
            'watering' => WateringFrequency::class,
            'difficulty' => DifficultyLevel::class,
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function scopeActive($query): void
    {
        $query->where('is_active', true);
    }

    public function scopeFeatured($query): void
    {
        $query->where('is_featured', true);
    }

    public function scopeNewArrivals($query): void
    {
        $query->where('is_new_arrival', true);
    }

    public function scopeInStock($query): void
    {
        $query->where('stock_quantity', '>', 0);
    }

    public function scopeLowStock($query): void
    {
        $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->where('stock_quantity', '>', 0);
    }

    public function getNameAttribute(): ?string
    {
        return $this->getTranslation('name');
    }

    public function getShortDescriptionAttribute(): ?string
    {
        return $this->getTranslation('short_description');
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->getTranslation('description');
    }

    public function getPrimaryImageAttribute(): ?ProductImage
    {
        return $this->images->firstWhere('is_primary', true) ?? $this->images->first();
    }

    public function getFormattedPriceAttribute(): string
    {
        return '৳'.number_format((float) $this->price, 2);
    }

    public function getDiscountPercentageAttribute(): int
    {
        if (! $this->compare_price || $this->compare_price <= $this->price) {
            return 0;
        }

        return (int) round((($this->compare_price - $this->price) / $this->compare_price) * 100);
    }

    public function getAverageRatingAttribute(): float
    {
        if (array_key_exists('reviews_avg_rating', $this->attributes)) {
            return round((float) ($this->attributes['reviews_avg_rating'] ?? 0), 1);
        }

        return round($this->reviews()->where('is_approved', true)->avg('rating') ?? 0, 1);
    }

    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }
}
