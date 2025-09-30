<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VosizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create categories first
        $categories = [
            [
                'name' => 'Face Care',
                'slug' => 'face-care',
                'description' => 'Premium facial products designed for modern men. From cleansers to serums, everything you need for healthy skin.',
                'image' => '/images/categories/face-care.jpg',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Body Care',
                'slug' => 'body-care',
                'description' => 'Essential body care products for everyday grooming and wellness.',
                'image' => '/images/categories/body-care.jpg',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Hair Care',
                'slug' => 'hair-care',
                'description' => 'Professional hair styling and care products for all hair types.',
                'image' => '/images/categories/hair-care.jpg',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Beard Care',
                'slug' => 'beard-care',
                'description' => 'Complete beard grooming essentials for the modern gentleman.',
                'image' => '/images/categories/beard-care.jpg',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Shaving',
                'slug' => 'shaving',
                'description' => 'Premium shaving products and accessories for the perfect shave.',
                'image' => '/images/categories/shaving.jpg',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = \App\Models\Category::create($categoryData);
            
            // Create 3-5 products for each category
            \App\Models\Product::factory(rand(3, 5))->create([
                'category_id' => $category->id,
            ]);
        }

        // Create a featured collection
        \App\Models\Product::whereIn('id', \App\Models\Product::inRandomOrder()->limit(6)->pluck('id'))
            ->update(['is_featured' => true]);
    }
}
