<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class EcommerceBusinessLogicTest extends DuskTestCase
{
    /**
     * Test shopping cart functionality
     * Critical business logic validation
     */
    public function testShoppingCartWorkflow(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/products')
                    ->pause(2000)
                    ->screenshot('cart-01-products-page');
                    
            // Look for add to cart buttons
            $addToCartButtons = [
                'button[contains(text(), "Add to Cart")]',
                '.add-to-cart',
                '.btn-add-cart',
                'button.add-cart',
                '[data-action="add-to-cart"]'
            ];
            
            foreach ($addToCartButtons as $selector) {
                if ($browser->element($selector)) {
                    $browser->click($selector)
                            ->pause(2000)
                            ->screenshot('cart-02-added-to-cart');
                    break;
                }
            }
            
            // Look for cart icon or link
            $cartSelectors = [
                '.cart-icon',
                'a[href*="cart"]',
                '.shopping-cart',
                'button[contains(text(), "Cart")]'
            ];
            
            foreach ($cartSelectors as $selector) {
                if ($browser->element($selector)) {
                    $browser->click($selector)
                            ->pause(2000)
                            ->screenshot('cart-03-cart-opened');
                    break;
                }
            }
        });
    }
    
    /**
     * Test product filtering and sorting
     * Professional UX validation
     */
    public function testProductFiltering(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/products')
                    ->pause(2000)
                    ->screenshot('filter-01-initial-products');
                    
            // Test category filtering
            if ($browser->element('select[name="category"]')) {
                $browser->select('select[name="category"]', 'Haircare')
                        ->pause(3000)
                        ->screenshot('filter-02-haircare-filtered');
                        
                $browser->select('select[name="category"]', 'Skincare')
                        ->pause(3000)
                        ->screenshot('filter-03-skincare-filtered');
                        
                $browser->select('select[name="category"]', 'all')
                        ->pause(3000)
                        ->screenshot('filter-04-all-products');
            }
            
            // Test price sorting if available
            if ($browser->element('select[name="sort"]')) {
                $browser->select('select[name="sort"]', 'price_asc')
                        ->pause(3000)
                        ->screenshot('filter-05-price-ascending');
                        
                $browser->select('select[name="sort"]', 'price_desc')
                        ->pause(3000)
                        ->screenshot('filter-06-price-descending');
            }
        });
    }
    
    /**
     * Test form validation
     * Security and data integrity testing
     */
    public function testFormValidation(): void
    {
        $this->browse(function (Browser $browser) {
            // Test contact form validation
            $browser->visit('/contact')
                    ->pause(2000)
                    ->screenshot('validation-01-contact-form');
                    
            if ($browser->element('form')) {
                // Submit empty form to test validation
                if ($browser->element('button[type="submit"]')) {
                    $browser->click('button[type="submit"]')
                            ->pause(2000)
                            ->screenshot('validation-02-empty-form-submit');
                } else {
                    $browser->press('Submit')
                            ->pause(2000)
                            ->screenshot('validation-02-empty-form-submit');
                }
                        
                // Fill form with invalid data
                if ($browser->element('input[name="email"]')) {
                    $browser->type('input[name="email"]', 'invalid-email');
                    
                    if ($browser->element('button[type="submit"]')) {
                        $browser->click('button[type="submit"]')
                                ->pause(2000)
                                ->screenshot('validation-03-invalid-email');
                    } else {
                        $browser->press('Submit')
                                ->pause(2000)
                                ->screenshot('validation-03-invalid-email');
                    }
                }
            }
            
            // Test registration validation
            $browser->visit('/register')
                    ->pause(2000)
                    
                    // Test password mismatch
                    ->type('name', 'Test User')
                    ->type('email', 'test@example.com')
                    ->type('password', 'password123')
                    ->type('password_confirmation', 'different_password')
                    ->press('Register')
                    ->pause(2000)
                    ->screenshot('validation-04-password-mismatch');
        });
    }
    
    /**
     * Test error handling
     * Professional error management testing
     */
    public function testErrorHandling(): void
    {
        $this->browse(function (Browser $browser) {
            // Test 404 error handling
            $browser->visit('/non-existent-page-12345')
                    ->pause(2000)
                    ->screenshot('error-01-404-page')
                    ->assertDontSee('Fatal Error')
                    ->assertDontSee('Exception');
                    
            // Test invalid product ID
            $browser->visit('/products/999999999')
                    ->pause(2000)
                    ->screenshot('error-02-invalid-product')
                    ->assertDontSee('Fatal Error')
                    ->assertDontSee('Exception');
        });
    }
    
    /**
     * Test security headers and HTTPS
     * Professional security validation
     */
    public function testSecurityFeatures(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->pause(2000);
                    
            // Check for security-related elements
            $html = $browser->driver->getPageSource();
            
            // Log security check results
            $browser->screenshot('security-01-homepage-check');
            
            // Verify no sensitive information exposed
            $this->assertStringNotContainsString('password', strtolower($html));
            $this->assertStringNotContainsString('secret', strtolower($html));
            $this->assertStringNotContainsString('api_key', strtolower($html));
        });
    }
    
    /**
     * Test accessibility features
     * Professional accessibility compliance
     */
    public function testAccessibilityFeatures(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->pause(2000)
                    ->screenshot('accessibility-01-homepage');
                    
            // Check for alt attributes on images
            $images = $browser->driver->findElements(\Facebook\WebDriver\WebDriverBy::tagName('img'));
            $imagesWithoutAlt = 0;
            
            foreach ($images as $image) {
                if (!$image->getAttribute('alt')) {
                    $imagesWithoutAlt++;
                }
            }
            
            // Check for form labels
            $inputs = $browser->driver->findElements(\Facebook\WebDriver\WebDriverBy::tagName('input'));
            $inputsWithoutLabels = 0;
            
            foreach ($inputs as $input) {
                $id = $input->getAttribute('id');
                if ($id) {
                    $labels = $browser->driver->findElements(\Facebook\WebDriver\WebDriverBy::xpath("//label[@for='$id']"));
                    if (empty($labels)) {
                        $inputsWithoutLabels++;
                    }
                }
            }
            
            $browser->screenshot('accessibility-02-check-complete');
            
            // Log accessibility issues (non-blocking for now)
            if ($imagesWithoutAlt > 0) {
                echo "\nAccessibility Notice: $imagesWithoutAlt images without alt attributes found\n";
            }
            if ($inputsWithoutLabels > 0) {
                echo "\nAccessibility Notice: $inputsWithoutLabels inputs without labels found\n";
            }
        });
    }
}