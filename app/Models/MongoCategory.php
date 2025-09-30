<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class MongoCategory extends Model
{
    use SoftDeletes;

    protected $connection = 'mongodb';
    protected $collection = 'categories';
    
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'parent_id',
        'status',
        'sort_order',
        'meta_title',
        'meta_description'
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function products()
    {
        return $this->hasMany(MongoProduct::class, 'category_id');
    }

    public function parent()
    {
        return $this->belongsTo(MongoCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MongoCategory::class, 'parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeRootCategories($query)
    {
        return $query->whereNull('parent_id');
    }
}