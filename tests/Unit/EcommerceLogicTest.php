<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class EcommerceLogicTest extends TestCase
{
    /**
     * Test product price calculation
     */
    public function testProductPriceCalculation(): void
    {
        // Test regular price
        $price = 100.00;
        $discount = 0;
        $finalPrice = $this->calculateFinalPrice($price, $discount);
        
        $this->assertEquals(100.00, $finalPrice);
    }
    
    /**
     * Test product discount calculation
     */
    public function testProductDiscountCalculation(): void
    {
        // Test with 20% discount
        $price = 100.00;
        $discount = 20;
        $finalPrice = $this->calculateFinalPrice($price, $discount);
        
        $this->assertEquals(80.00, $finalPrice);
    }
    
    /**
     * Test shipping cost calculation
     */
    public function testShippingCostCalculation(): void
    {
        // Free shipping over $50
        $orderTotal = 75.00;
        $shippingCost = $this->calculateShipping($orderTotal);
        
        $this->assertEquals(0.00, $shippingCost);
        
        // $10 shipping under $50
        $orderTotal = 30.00;
        $shippingCost = $this->calculateShipping($orderTotal);
        
        $this->assertEquals(10.00, $shippingCost);
    }
    
    /**
     * Test tax calculation
     */
    public function testTaxCalculation(): void
    {
        $subtotal = 100.00;
        $taxRate = 8.5; // 8.5% tax
        $tax = $this->calculateTax($subtotal, $taxRate);
        
        $this->assertEquals(8.50, $tax);
    }
    
    /**
     * Test email validation
     */
    public function testEmailValidation(): void
    {
        $this->assertTrue($this->isValidEmail('user@example.com'));
        $this->assertTrue($this->isValidEmail('test.email+tag@domain.co.uk'));
        $this->assertFalse($this->isValidEmail('invalid-email'));
        $this->assertFalse($this->isValidEmail(''));
    }
    
    /**
     * Test product slug generation
     */
    public function testProductSlugGeneration(): void
    {
        $productName = 'Premium Beard Oil - Cedar & Sage';
        $slug = $this->generateSlug($productName);
        
        $this->assertEquals('premium-beard-oil-cedar-sage', $slug);
    }
    
    // Helper methods that simulate your business logic
    private function calculateFinalPrice(float $price, int $discountPercent): float
    {
        if ($discountPercent > 0) {
            return $price - ($price * $discountPercent / 100);
        }
        return $price;
    }
    
    private function calculateShipping(float $orderTotal): float
    {
        return $orderTotal >= 50 ? 0.00 : 10.00;
    }
    
    private function calculateTax(float $subtotal, float $taxRate): float
    {
        return round($subtotal * $taxRate / 100, 2);
    }
    
    private function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    private function generateSlug(string $text): string
    {
        $slug = strtolower($text);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        return trim($slug, '-');
    }
}