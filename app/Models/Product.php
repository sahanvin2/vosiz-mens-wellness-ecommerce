<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'supplier_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'compare_price',
        'stock_quantity',
        'sku',
        'images',
        'ingredients',
        'benefits',
        'skin_type',
        'is_featured',
        'is_active',
        'weight',
        'dimensions',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'images' => 'array',
        'ingredients' => 'array',
        'benefits' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    // Accessor for main image
    public function getMainImageAttribute(): ?string
    {
        return $this->images[0] ?? null;
    }

    // Accessor for discount percentage
    public function getDiscountPercentageAttribute(): int
    {
        if ($this->compare_price && $this->compare_price > $this->price) {
            return round((($this->compare_price - $this->price) / $this->compare_price) * 100);
        }
        return 0;
    }

    // Check if product is in stock
    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }
}
