<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use App\Models\MongoDBProduct;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users and products for creating test orders
        $users = User::limit(3)->get();
        $products = Product::limit(5)->get();
        $mongoProducts = MongoDBProduct::limit(5)->get();
        
        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        // Create test orders
        foreach ($users as $user) {
            // Create 2-3 orders per user
            for ($i = 0; $i < rand(2, 3); $i++) {
                $order = Order::create([
                    'user_id' => $user->id,
                    'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                    'status' => collect(['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->random(),
                    'payment_status' => collect(['pending', 'paid', 'failed'])->random(),
                    'payment_method' => collect(['credit_card', 'paypal', 'stripe'])->random(),
                    'subtotal' => 0, // Will be calculated below
                    'tax_amount' => 0,
                    'shipping_cost' => rand(0, 1500) / 100, // $0.00 to $15.00
                    'total_amount' => 0, // Will be calculated below
                    'shipping_address' => [
                        'street' => fake()->streetAddress(),
                        'city' => fake()->city(),
                        'state' => fake()->state(),
                        'zip' => fake()->postcode(),
                        'country' => 'United States'
                    ],
                    'billing_address' => [
                        'street' => fake()->streetAddress(),
                        'city' => fake()->city(),
                        'state' => fake()->state(),
                        'zip' => fake()->postcode(),
                        'country' => 'United States'
                    ],
                    'notes' => rand(0, 1) ? fake()->sentence() : null,
                    'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
                ]);

                $subtotal = 0;

                // Add 1-4 items to each order
                $itemCount = rand(1, 4);
                $usedProducts = [];

                for ($j = 0; $j < $itemCount; $j++) {
                    // Only use regular products for order items (MongoDB products can't be linked properly)
                    if ($products->isEmpty()) {
                        continue; // Skip if no products available
                    }
                    
                    $product = $products->random();
                    
                    // Skip if already used in this order
                    if (in_array($product->id, $usedProducts)) {
                        continue;
                    }
                    $usedProducts[] = $product->id;
                    
                    $quantity = rand(1, 3);
                    $price = $product->price;
                    $itemTotal = $price * $quantity;
                    $subtotal += $itemTotal;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $price,
                        'total' => $itemTotal,
                    ]);
                }

                // Calculate tax (8% for example)
                $taxAmount = $subtotal * 0.08;
                $totalAmount = $subtotal + $taxAmount + $order->shipping_cost;

                // Update order totals
                $order->update([
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $totalAmount,
                ]);

                $this->command->info("Created order {$order->order_number} for user {$user->name} with {$itemCount} items (Total: \${$totalAmount})");
            }
        }

        $this->command->info('Order seeding completed!');
    }
}