<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            ['name' => 'Face Care', 'description' => 'Premium facial products for modern men'],
            ['name' => 'Body Care', 'description' => 'Essential body care products for everyday use'],
            ['name' => 'Hair Care', 'description' => 'Professional hair styling and care products'],
            ['name' => 'Beard Care', 'description' => 'Complete beard grooming essentials'],
            ['name' => 'Shaving', 'description' => 'Premium shaving products and accessories'],
        ];

        $category = $this->faker->randomElement($categories);
        
        return [
            'name' => $category['name'],
            'slug' => \Illuminate\Support\Str::slug($category['name']),
            'description' => $category['description'],
            'image' => '/images/categories/' . \Illuminate\Support\Str::slug($category['name']) . '.jpg',
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
