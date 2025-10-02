<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MongoProduct extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'products';  // MongoDB collection name

    protected $fillable = [
        '_id',
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
        'is_active',
        'is_featured',
        'stock_quantity',
        'weight',
        'dimensions',
        'rating_average',
        'rating_count',
        'views_count',
        'sales_count',
        'document_data',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'discount_percentage' => 'integer',
        'is_active' => 'boolean',
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
        'document_data' => 'array',
    ];

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

    // Scope for active products
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for featured products
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Scope for products in stock
    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    // Search scope
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', '%' . $term . '%')
              ->orWhere('description', 'like', '%' . $term . '%')
              ->orWhere('short_description', 'like', '%' . $term . '%')
              ->orWhereJsonContains('tags', $term);
        });
    }

    // ==================== MongoDB-Style Methods ====================

    /**
     * MongoDB-style find by ObjectId
     */
    public static function findByObjectId(string $objectId)
    {
        return static::where('_id', $objectId)->first();
    }

    /**
     * MongoDB-style insertOne
     */
    public static function insertOne(array $document)
    {
        if (!isset($document['_id'])) {
            $document['_id'] = object_id();
        }
        
        return static::create($document);
    }

    /**
     * MongoDB-style updateOne
     */
    public function updateOne(array $update)
    {
        return $this->update($update);
    }

    /**
     * MongoDB-style deleteOne
     */
    public function deleteOne()
    {
        return $this->delete();
    }

    /**
     * MongoDB-style text search
     */
    public static function textSearch(string $searchTerm)
    {
        return static::where(function ($query) use ($searchTerm) {
            $query->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('sku', 'like', "%{$searchTerm}%")
                  ->orWhere('category_name', 'like', "%{$searchTerm}%")
                  ->orWhereJsonContains('tags', $searchTerm);
        });
    }

    /**
     * Get document as MongoDB-style array
     */
    public function toDocument()
    {
        $document = $this->toArray();
        $document['_id'] = $this->_id ?? $this->id;
        return $document;
    }

    /**
     * MongoDB-style aggregation pipeline
     */
    public static function aggregate(array $pipeline)
    {
        $query = static::query();
        
        foreach ($pipeline as $stage) {
            if (isset($stage['$match'])) {
                foreach ($stage['$match'] as $field => $value) {
                    if (is_array($value)) {
                        foreach ($value as $operator => $operandValue) {
                            switch ($operator) {
                                case '$gte':
                                    $query->where($field, '>=', $operandValue);
                                    break;
                                case '$lte':
                                    $query->where($field, '<=', $operandValue);
                                    break;
                                case '$in':
                                    $query->whereIn($field, $operandValue);
                                    break;
                                case '$regex':
                                    $query->where($field, 'like', "%{$operandValue}%");
                                    break;
                            }
                        }
                    } else {
                        $query->where($field, $value);
                    }
                }
            }
            
            if (isset($stage['$sort'])) {
                foreach ($stage['$sort'] as $field => $direction) {
                    $query->orderBy($field, $direction === 1 ? 'asc' : 'desc');
                }
            }
            
            if (isset($stage['$limit'])) {
                $query->limit($stage['$limit']);
            }
        }
        
        return $query->get();
    }

    /**
     * Boot method to auto-generate _id and maintain document_data
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->_id) {
                $model->_id = object_id();
            }
        });

        static::saving(function ($model) {
            // Keep document_data in sync with model attributes
            $model->document_data = $model->toArray();
        });
    }
}