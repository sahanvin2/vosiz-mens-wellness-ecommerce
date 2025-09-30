<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class MongoDBProduct extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'products';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'sale_price',
        'discount_percentage',
        'sku',
        'category_id',
        'category_name',
        'images',
        'video_url',
        'introduction_video',
        'features',
        'specifications',
        'ingredients',
        'usage_instructions',
        'tags',
        'meta_title',
        'meta_description',
        'status',
        'is_featured',
        'stock_quantity',
        'weight',
        'dimensions',
        'rating_average',
        'rating_count',
        'views_count',
        'sales_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'discount_percentage' => 'integer',
        'status' => 'string',
        'is_featured' => 'boolean',
        'stock_quantity' => 'integer',
        'rating_average' => 'decimal:1',
        'rating_count' => 'integer',
        'views_count' => 'integer',
        'sales_count' => 'integer',
        'images' => 'array',
        'features' => 'array',
        'specifications' => 'array',
        'ingredients' => 'array',
        'tags' => 'array',
        'dimensions' => 'array',
    ];

    // MongoDB-specific methods
    public function getFormattedPriceAttribute()
    {
        return number_format((float)$this->price, 2);
    }

    public function getFormattedSalePriceAttribute()
    {
        return $this->sale_price ? number_format((float)$this->sale_price, 2) : null;
    }

    public function getMainImageAttribute()
    {
        return $this->images && count($this->images) > 0 ? $this->images[0] : null;
    }

    public function getFinalPriceAttribute()
    {
        return $this->sale_price ?: $this->price;
    }

    public function getDiscountAmountAttribute()
    {
        if ($this->sale_price && $this->price > $this->sale_price) {
            return $this->price - $this->sale_price;
        }
        return 0;
    }

    public function hasDiscount()
    {
        return $this->sale_price && $this->sale_price < $this->price;
    }

    public function isInStock()
    {
        return $this->stock_quantity > 0;
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function incrementSales($quantity = 1)
    {
        $this->increment('sales_count', $quantity);
    }

    // MongoDB Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', '%' . $term . '%')
              ->orWhere('description', 'like', '%' . $term . '%')
              ->orWhere('short_description', 'like', '%' . $term . '%')
              ->orWhere('tags', $term);
        });
    }

    // MongoDB Aggregation Methods
    public static function getProductStats()
    {
        return [
            'total_products' => static::count(),
            'active_products' => static::where('is_active', true)->count(),
            'featured_products' => static::where('is_featured', true)->count(),
            'out_of_stock' => static::where('stock_quantity', 0)->count(),
            'average_price' => static::avg('price'),
            'total_views' => static::sum('views_count'),
            'total_sales' => static::sum('sales_count'),
        ];
    }

    public static function getTopSellingProducts($limit = 10)
    {
        return static::where('is_active', true)
                    ->orderBy('sales_count', 'desc')
                    ->limit($limit)
                    ->get();
    }

    public static function getProductsByPriceRange($min, $max)
    {
        return static::where('is_active', true)
                    ->whereBetween('price', [$min, $max])
                    ->get();
    }

    public static function searchProducts($searchTerm)
    {
        return static::where('is_active', true)
                    ->where(function($query) use ($searchTerm) {
                        $query->where('name', 'regex', "/$searchTerm/i")
                              ->orWhere('description', 'regex', "/$searchTerm/i")
                              ->orWhere('tags', 'in', [$searchTerm]);
                    })
                    ->get();
    }
}