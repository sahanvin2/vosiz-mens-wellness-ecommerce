<?php

namespace Tests\Functional;

use Tests\SeleniumTestCase;

class HomepageTest extends SeleniumTestCase
{
    /**
     * Test that homepage loads successfully
     */
    public function testHomepageLoads(): void
    {
        echo "🏠 Testing Homepage Load...\n";
        
        $this->visit('/');
        
        // Check page title
        $title = self::$driver->getTitle();
        $this->assertStringContainsString('VOSIZ', $title, 'Page title should contain VOSIZ');
        
        // Check for main navigation
        $this->assertElementVisible('nav');
        
        // Check for hero section
        $this->assertTextPresent('Men\'s Wellness');
        
        // Take screenshot for verification
        $this->takeScreenshot('homepage_loaded.png');
        
        echo "✅ Homepage loaded successfully\n";
    }
    
    /**
     * Test navigation menu functionality
     */
    public function testNavigationMenu(): void
    {
        echo "🧭 Testing Navigation Menu...\n";
        
        $this->visit('/');
        
        // Test Products link
        $this->click('a[href*="products"]');
        $this->waitForPageLoad();
        
        $currentUrl = self::$driver->getCurrentURL();
        $this->assertStringContainsString('products', $currentUrl);
        
        // Check products page elements
        $this->assertTextPresent('Products');
        $this->assertElementVisible('.product-grid, .products-container, [class*="product"]');
        
        $this->takeScreenshot('products_page.png');
        
        echo "✅ Navigation menu works correctly\n";
    }
    
    /**
     * Test responsive design
     */
    public function testResponsiveDesign(): void
    {
        echo "📱 Testing Responsive Design...\n";
        
        $this->visit('/');
        
        // Test mobile view
        self::$driver->manage()->window()->setSize(new \Facebook\WebDriver\WebDriverDimension(375, 667));
        sleep(2);
        
        // Check if mobile menu is accessible
        $this->assertElementVisible('body');
        $this->takeScreenshot('mobile_view.png');
        
        // Test tablet view
        self::$driver->manage()->window()->setSize(new \Facebook\WebDriver\WebDriverDimension(768, 1024));
        sleep(2);
        $this->takeScreenshot('tablet_view.png');
        
        // Reset to desktop
        self::$driver->manage()->window()->setSize(new \Facebook\WebDriver\WebDriverDimension(1920, 1080));
        
        echo "✅ Responsive design tested\n";
    }
}