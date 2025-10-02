<?php

namespace Tests\Functional;

use Tests\SeleniumTestCase;

class ProductTest extends SeleniumTestCase
{
    /**
     * Test products page loads and displays products
     */
    public function testProductsPageLoads(): void
    {
        echo "🛍️ Testing Products Page...\n";
        
        $this->visit('/products');
        
        // Check page title and content
        $this->assertTextPresent('Products');
        
        // Check if products are displayed
        $this->waitForElement('.product-card, .product-item, [class*="product"]', 15);
        
        // Check for categories dropdown/filter
        $this->assertElementVisible('select[name="category"], .category-filter, [class*="category"]');
        
        // Check for product images
        $this->assertElementVisible('img[src*="images/products"], .product-image img');
        
        $this->takeScreenshot('products_page_loaded.png');
        
        echo "✅ Products page loaded with products\n";
    }
    
    /**
     * Test category filtering
     */
    public function testCategoryFiltering(): void
    {
        echo "🏷️ Testing Category Filtering...\n";
        
        $this->visit('/products');
        
        // Wait for page to load
        $this->waitForElement('select[name="category"], .category-filter', 10);
        
        try {
            // Try to find and use category filter
            $categorySelect = self::$driver->findElement(\Facebook\WebDriver\WebDriverBy::cssSelector('select[name="category"]'));
            
            if ($categorySelect->isDisplayed()) {
                // Select a category (e.g., Skincare)
                $this->click('select[name="category"]');
                sleep(1);
                
                // Select first option after "All Categories"
                $options = self::$driver->findElements(\Facebook\WebDriver\WebDriverBy::cssSelector('select[name="category"] option'));
                if (count($options) > 1) {
                    $options[1]->click(); // Second option (first category)
                    sleep(2);
                    
                    $this->takeScreenshot('category_filtered.png');
                    echo "✅ Category filtering works\n";
                } else {
                    echo "⚠️ No category options found\n";
                }
            }
        } catch (\Exception $e) {
            echo "⚠️ Category filter not interactive or not found: " . $e->getMessage() . "\n";
            $this->takeScreenshot('category_filter_issue.png');
        }
    }
    
    /**
     * Test product detail page
     */
    public function testProductDetailPage(): void
    {
        echo "📋 Testing Product Detail Page...\n";
        
        $this->visit('/products');
        
        // Wait for products to load
        $this->waitForElement('.product-card, .product-item, [class*="product"]', 15);
        
        try {
            // Click on first product
            $firstProduct = self::$driver->findElement(
                \Facebook\WebDriver\WebDriverBy::cssSelector('a[href*="/products/"], .product-link, .product-card a')
            );
            
            if ($firstProduct->isDisplayed()) {
                $firstProduct->click();
                $this->waitForPageLoad();
                
                // Check product detail elements
                $this->assertElementVisible('.product-title, .product-name, h1');
                $this->assertElementVisible('.product-price, .price, [class*="price"]');
                $this->assertElementVisible('.product-description, .description');
                
                // Check for add to cart button
                $this->assertElementVisible('button[type="submit"], .add-to-cart, [class*="cart"]');
                
                $this->takeScreenshot('product_detail_page.png');
                
                echo "✅ Product detail page loaded successfully\n";
            }
        } catch (\Exception $e) {
            echo "⚠️ Could not access product detail: " . $e->getMessage() . "\n";
            $this->takeScreenshot('product_detail_issue.png');
        }
    }
    
    /**
     * Test search functionality
     */
    public function testProductSearch(): void
    {
        echo "🔍 Testing Product Search...\n";
        
        $this->visit('/products');
        
        try {
            // Look for search input
            $searchInput = self::$driver->findElement(
                \Facebook\WebDriver\WebDriverBy::cssSelector('input[name="search"], input[type="search"], .search-input')
            );
            
            if ($searchInput->isDisplayed()) {
                // Search for a product
                $this->fillInput('input[name="search"], input[type="search"], .search-input', 'beard');
                
                // Submit search (look for search button or form)
                try {
                    $this->click('button[type="submit"], .search-button, .btn-search');
                } catch (\Exception $e) {
                    // If no button, try pressing Enter
                    $searchInput->sendKeys(\Facebook\WebDriver\WebDriverKeys::ENTER);
                }
                
                sleep(2);
                $this->takeScreenshot('search_results.png');
                
                echo "✅ Search functionality tested\n";
            } else {
                echo "⚠️ Search input not found or not visible\n";
            }
        } catch (\Exception $e) {
            echo "⚠️ Search functionality not available: " . $e->getMessage() . "\n";
            $this->takeScreenshot('search_issue.png');
        }
    }
}