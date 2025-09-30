<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Video extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'videos';

    protected $fillable = [
        'title',
        'description',
        'video_url',
        'video_file',
        'thumbnail',
        'type', // 'hero', 'product_intro', 'category_intro'
        'category_id',
        'product_id',
        'duration',
        'file_size',
        'dimensions',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'dimensions' => 'array',
    ];

    // Scope for active videos
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for hero videos
    public function scopeHero($query)
    {
        return $query->where('type', 'hero');
    }

    // Scope for product intro videos
    public function scopeProductIntro($query)
    {
        return $query->where('type', 'product_intro');
    }

    // Scope for category intro videos
    public function scopeCategoryIntro($query)
    {
        return $query->where('type', 'category_intro');
    }

    // Get formatted file size
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) return 'Unknown';
        
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    // Get formatted duration
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration) return 'Unknown';
        
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;
        
        return sprintf('%d:%02d', $minutes, $seconds);
    }
}