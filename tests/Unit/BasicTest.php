<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase
{
    /**
     * Test that our testing environment is working
     */
    public function testTestingEnvironmentWorks(): void
    {
        $this->assertTrue(true, 'Testing environment is working');
    }
    
    /**
     * Test basic PHP functionality
     */
    public function testBasicPHPFunctionality(): void
    {
        $result = 2 + 2;
        $this->assertEquals(4, $result, 'Basic math should work');
    }
    
    /**
     * Test string operations
     */
    public function testStringOperations(): void
    {
        $websiteName = 'VOSIZ';
        $this->assertStringContainsString('VOSIZ', $websiteName);
        $this->assertEquals(5, strlen($websiteName));
    }
    
    /**
     * Test array operations
     */
    public function testArrayOperations(): void
    {
        $categories = ['Skincare', 'Beard Care', 'Hair Care', 'Body Care', 'Grooming Tools'];
        
        $this->assertCount(5, $categories);
        $this->assertContains('Skincare', $categories);
        $this->assertNotContains('Electronics', $categories);
    }
    
    /**
     * Test that environment variables can be accessed
     */
    public function testEnvironmentAccess(): void
    {
        // Set a test environment variable
        $_ENV['TEST_VAR'] = 'test_value';
        
        $this->assertEquals('test_value', $_ENV['TEST_VAR']);
        $this->assertNotEmpty($_ENV);
    }
}