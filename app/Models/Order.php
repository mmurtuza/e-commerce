<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'order_number', 'status', 'payment_status', 'payment_method',
        'subtotal', 'discount_amount', 'shipping_amount', 'tax_amount', 'total',
        'coupon_id', 'shipping_address', 'billing_address', 'notes',
        'shipped_at', 'delivered_at', 'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_status' => PaymentStatus::class,
            'payment_method' => PaymentMethod::class,
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'shipping_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'shipping_address' => 'array',
            'billing_address' => 'array',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function shippingMethod(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeByStatus($query, OrderStatus $status): void
    {
        $query->where('status', $status);
    }

    // Shipping address accessors (data stored as JSON array)

    public function getShippingNameAttribute(): ?string
    {
        return $this->shipping_address['full_name'] ?? null;
    }

    public function getShippingPhoneAttribute(): ?string
    {
        return $this->shipping_address['phone'] ?? null;
    }

    public function getShippingCityAttribute(): ?string
    {
        return $this->shipping_address['city'] ?? null;
    }

    public function getShippingDistrictAttribute(): ?string
    {
        return $this->shipping_address['district'] ?? null;
    }

    public function getShippingDivisionAttribute(): ?string
    {
        return $this->shipping_address['division'] ?? null;
    }

    public function getShippingFullAddressAttribute(): string
    {
        $addr = $this->shipping_address ?? [];
        return implode(', ', array_filter([
            $addr['address_line_1'] ?? null,
            $addr['address_line_2'] ?? null,
            $addr['city'] ?? null,
            $addr['district'] ?? null,
            $addr['division'] ?? null,
            $addr['postal_code'] ?? null,
        ]));
    }
}
