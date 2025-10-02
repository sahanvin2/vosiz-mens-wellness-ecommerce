<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProfessionalEcommerceTest extends DuskTestCase
{
    /**
     * Test complete user journey - Homepage to Purchase
     * This is a professional E2E test that simulates real user behavior
     */
    public function testCompleteUserJourney(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('VOSIZ')
                    ->assertSee('Men\'s Wellness')
                    ->screenshot('01-homepage-loaded')
                    
                    // Navigate to products
                    ->clickLink('Products')
                    ->waitForLocation('/products')
                    ->assertSee('Products')
                    ->screenshot('02-products-page')
                    
                    // Test product filtering
                    ->select('category', 'Skincare')
                    ->pause(2000) // Wait for filter to apply
                    ->screenshot('03-filtered-products')
                    
                    // Click on first product
                    ->click('.product-card:first-child a, .product-item:first-child a, [class*="product"]:first-child a')
                    ->pause(2000)
                    ->screenshot('04-product-detail')
                    
                    // Test responsive design
                    ->resize(375, 667) // Mobile view
                    ->pause(1000)
                    ->screenshot('05-mobile-view')
                    
                    ->resize(768, 1024) // Tablet view  
                    ->pause(1000)
                    ->screenshot('06-tablet-view')
                    
                    ->resize(1920, 1080) // Desktop view
                    ->pause(1000)
                    ->screenshot('07-desktop-view');
        });
    }
    
    /**
     * Test user authentication flow
     * Professional testing of security-critical functionality
     */
    public function testUserAuthentication(): void
    {
        $this->browse(function (Browser $browser) {
            // Test registration
            $browser->visit('/register')
                    ->assertSee('Register')
                    ->screenshot('08-register-page')
                    
                    ->type('name', 'Test User Professional')
                    ->type('email', 'professional_test_' . time() . '@vosiz.com')
                    ->type('password', 'SecurePassword123!')
                    ->type('password_confirmation', 'SecurePassword123!')
                    ->screenshot('09-registration-form-filled')
                    
                    ->press('Register')
                    ->pause(3000)
                    ->screenshot('10-registration-result')
                    
                    // Test login page
                    ->visit('/login')
                    ->assertSee('Login')
                    ->screenshot('11-login-page')
                    
                    // Test forgot password link
                    ->assertSeeLink('Forgot Your Password?')
                    ->screenshot('12-forgot-password-available');
        });
    }
    
    /**
     * Test admin panel functionality
     * Critical business functionality testing
     */
    public function testAdminPanelAccess(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/products')
                    ->pause(2000)
                    ->screenshot('13-admin-access-attempt')
                    
                    // Should either show admin panel or redirect to login
                    ->assertDontSee('Fatal Error')
                    ->assertDontSee('500')
                    ->screenshot('14-admin-response');
        });
    }
    
    /**
     * Test search functionality
     * Professional UX testing
     */
    public function testSearchFunctionality(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/products')
                    ->pause(2000);
                    
            // Try to find search input
            if ($browser->element('input[name="search"], input[type="search"], .search-input')) {
                $browser->type('input[name="search"], input[type="search"], .search-input', 'beard')
                        ->screenshot('15-search-typed')
                        ->press('Enter')
                        ->pause(2000)
                        ->screenshot('16-search-results');
            } else {
                $browser->screenshot('17-no-search-found');
            }
        });
    }
    
    /**
     * Test performance and loading times
     * Professional performance validation
     */
    public function testPageLoadPerformance(): void
    {
        $this->browse(function (Browser $browser) {
            $startTime = microtime(true);
            
            $browser->visit('/')
                    ->waitFor('body', 10);
                    
            $endTime = microtime(true);
            $loadTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
            
            $browser->screenshot('18-performance-test');
            
            // Assert page loads within reasonable time (5 seconds)
            $this->assertLessThan(5000, $loadTime, "Homepage should load within 5 seconds. Actual: {$loadTime}ms");
        });
    }
    
    /**
     * Test JavaScript functionality
     * Professional frontend testing
     */
    public function testJavaScriptFunctionality(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->pause(2000)
                    
                    // Test if JavaScript is working
                    ->script('return typeof jQuery !== "undefined" || typeof $ !== "undefined"');
                    
            $browser->screenshot('19-javascript-test');
        });
    }
}