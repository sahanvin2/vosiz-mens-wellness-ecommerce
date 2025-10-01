<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MongoDBProduct;

class SetupProductImages extends Command
{
    protected $signature = 'vosiz:setup-images';
    protected $description = 'Setup product images in MongoDB with Base64 encoding';

    public function handle()
    {
        $this->info('🖼️  Setting up product images...');

        // Sample base64 placeholder images (you'll replace these with real images)
        $sampleImages = [
            'beard-oil' => [
                'main' => $this->generatePlaceholderBase64('Premium Beard Oil', '#8B4513'),
                'gallery' => $this->generatePlaceholderBase64('Beard Oil Ingredients', '#DEB887')
            ],
            'face-wash' => [
                'main' => $this->generatePlaceholderBase64('Charcoal Face Wash', '#2F4F4F'),
                'gallery' => $this->generatePlaceholderBase64('Deep Cleansing', '#708090')
            ],
            'pomade' => [
                'main' => $this->generatePlaceholderBase64('Hair Pomade', '#CD853F'),
                'gallery' => $this->generatePlaceholderBase64('Strong Hold', '#DDD')
            ]
        ];

        // Update products with base64 images
        $products = [
            'premium-beard-oil' => 'beard-oil',
            'activated-charcoal-face-wash' => 'face-wash', 
            'moisturizing-hair-pomade' => 'pomade'
        ];

        foreach ($products as $slug => $imageKey) {
            try {
                $product = MongoDBProduct::where('slug', $slug)->first();
                if ($product) {
                    $product->update([
                        'images' => [
                            [
                                'type' => 'main',
                                'base64' => $sampleImages[$imageKey]['main'],
                                'filename' => $slug . '-main.jpg',
                                'alt_text' => $product->name . ' - Main Image',
                                'display_order' => 1
                            ],
                            [
                                'type' => 'gallery',
                                'base64' => $sampleImages[$imageKey]['gallery'],
                                'filename' => $slug . '-gallery.jpg', 
                                'alt_text' => $product->name . ' - Gallery Image',
                                'display_order' => 2
                            ]
                        ]
                    ]);
                    $this->info("✅ Updated images for: {$product->name}");
                }
            } catch (\Exception $e) {
                $this->error("❌ Failed to update {$slug}: " . $e->getMessage());
            }
        }

        $this->info('🎉 Product images setup complete!');
        $this->line('');
        $this->info('📋 Image Storage Structure:');
        $this->line('- Each product has an "images" array');
        $this->line('- Each image has: type, base64, filename, alt_text, display_order');
        $this->line('- Main images: Primary product photos');
        $this->line('- Gallery images: Additional product photos');
    }

    private function generatePlaceholderBase64($text, $bgColor = '#CCCCCC')
    {
        // Generate a simple SVG placeholder image as base64
        $svg = '<?xml version="1.0" encoding="UTF-8"?>
        <svg width="400" height="400" xmlns="http://www.w3.org/2000/svg">
            <rect width="400" height="400" fill="' . $bgColor . '"/>
            <text x="200" y="200" font-family="Arial" font-size="24" fill="white" text-anchor="middle" dominant-baseline="middle">
                ' . $text . '
            </text>
        </svg>';
        
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}