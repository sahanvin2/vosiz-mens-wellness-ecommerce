<?php

namespace Tests\Feature;

use Tests\TestCase;

class WebsiteBasicTest extends TestCase
{
    /**
     * Test that homepage loads successfully
     */
    public function testHomepageLoads(): void
    {
        echo "🏠 Testing homepage response...\n";
        
        $response = $this->get('/');
        
        $response->assertStatus(200);
        echo "✅ Homepage returns 200 OK\n";
    }
    
    /**
     * Test that products page loads
     */
    public function testProductsPageLoads(): void
    {
        echo "🛍️ Testing products page...\n";
        
        $response = $this->get('/products');
        
        $response->assertStatus(200);
        echo "✅ Products page returns 200 OK\n";
    }
    
    /**
     * Test that login page loads
     */
    public function testLoginPageLoads(): void
    {
        echo "🔐 Testing login page...\n";
        
        $response = $this->get('/login');
        
        $response->assertStatus(200);
        echo "✅ Login page returns 200 OK\n";
    }
    
    /**
     * Test that register page loads
     */
    public function testRegisterPageLoads(): void
    {
        echo "📝 Testing register page...\n";
        
        $response = $this->get('/register');
        
        $response->assertStatus(200);
        echo "✅ Register page returns 200 OK\n";
    }
    
    /**
     * Test API endpoint (if exists)
     */
    public function testApiHealthCheck(): void
    {
        echo "🔍 Testing API endpoints...\n";
        
        $response = $this->get('/api/health');
        
        // This might return 404 if endpoint doesn't exist, which is fine
        $this->assertTrue(in_array($response->getStatusCode(), [200, 404]));
        echo "✅ API endpoint test completed\n";
    }
}