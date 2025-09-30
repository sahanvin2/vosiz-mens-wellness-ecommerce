<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $products = [
            'Advanced Anti-Aging Serum',
            'Charcoal Deep Cleansing Face Wash',
            'Hydrating Face Moisturizer',
            'Premium Beard Oil',
            'Energizing Body Wash',
            'Hair Styling Pomade',
            'Aftershave Balm',
            'Exfoliating Face Scrub',
            'Vitamin C Eye Cream',
            'Natural Shampoo & Conditioner'
        ];

        $baseName = $this->faker->randomElement($products);
        $suffix = $this->faker->randomElement(['Pro', 'Advanced', 'Premium', 'Essential', 'Daily']);
        $name = $baseName . ' ' . $suffix . ' ' . $this->faker->numerify('##');
        $price = $this->faker->randomFloat(2, 15, 150);
        
        $skinTypes = ['dry', 'oily', 'combination', 'sensitive', 'normal'];
        $ingredients = [
            ['Vitamin C', 'Hyaluronic Acid', 'Niacinamide'],
            ['Charcoal', 'Tea Tree Oil', 'Salicylic Acid'],
            ['Aloe Vera', 'Jojoba Oil', 'Peptides'],
            ['Argan Oil', 'Cedarwood', 'Vitamin E'],
        ];

        $benefits = [
            ['Anti-aging', 'Hydrating', 'Brightening'],
            ['Deep cleansing', 'Oil control', 'Pore minimizing'],
            ['Moisturizing', 'Soothing', 'Protecting'],
            ['Nourishing', 'Softening', 'Strengthening'],
        ];

        return [
            'category_id' => \App\Models\Category::factory(),
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'description' => $this->faker->paragraph(3),
            'short_description' => $this->faker->sentence(),
            'price' => $price,
            'compare_price' => $this->faker->boolean(30) ? $price * 1.2 : null,
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'sku' => 'VOS-' . $this->faker->unique()->numerify('####'),
            'images' => [
                '/images/products/' . \Illuminate\Support\Str::slug($name) . '-1.jpg',
                '/images/products/' . \Illuminate\Support\Str::slug($name) . '-2.jpg',
            ],
            'ingredients' => $this->faker->randomElement($ingredients),
            'benefits' => $this->faker->randomElement($benefits),
            'skin_type' => $this->faker->randomElement($skinTypes),
            'is_featured' => $this->faker->boolean(20),
            'is_active' => true,
            'weight' => $this->faker->randomFloat(2, 50, 500),
            'dimensions' => $this->faker->numerify('##') . 'x' . $this->faker->numerify('##') . 'x' . $this->faker->numerify('##') . 'cm',
        ];
    }
}
