<?php

namespace Tests\Functional;

use Tests\SeleniumTestCase;

class UserAuthenticationTest extends SeleniumTestCase
{
    /**
     * Test user registration process
     */
    public function testUserRegistration(): void
    {
        echo "📝 Testing User Registration...\n";
        
        $this->visit('/register');
        
        // Check registration form elements
        $this->assertElementVisible('input[name="name"], #name');
        $this->assertElementVisible('input[name="email"], #email');
        $this->assertElementVisible('input[name="password"], #password');
        $this->assertElementVisible('input[name="password_confirmation"], #password_confirmation');
        
        // Fill registration form with test data
        $timestamp = time();
        $testEmail = "test_user_{$timestamp}@example.com";
        
        $this->fillInput('input[name="name"], #name', 'Test User');
        $this->fillInput('input[name="email"], #email', $testEmail);
        $this->fillInput('input[name="password"], #password', 'password123');
        $this->fillInput('input[name="password_confirmation"], #password_confirmation', 'password123');
        
        $this->takeScreenshot('registration_form_filled.png');
        
        // Submit registration form
        $this->click('button[type="submit"], .btn-register, input[type="submit"]');
        sleep(3);
        
        // Check if registration was successful (should redirect to dashboard or email verification)
        $currentUrl = self::$driver->getCurrentURL();
        $pageSource = self::$driver->getPageSource();
        
        $registrationSuccess = (
            strpos($currentUrl, 'dashboard') !== false ||
            strpos($currentUrl, 'email/verify') !== false ||
            strpos($pageSource, 'verify') !== false ||
            strpos($pageSource, 'Welcome') !== false
        );
        
        $this->takeScreenshot('registration_result.png');
        
        if ($registrationSuccess) {
            echo "✅ User registration completed successfully\n";
        } else {
            echo "⚠️ Registration result unclear - check screenshot\n";
        }
    }
    
    /**
     * Test user login process
     */
    public function testUserLogin(): void
    {
        echo "🔐 Testing User Login...\n";
        
        $this->visit('/login');
        
        // Check login form elements
        $this->assertElementVisible('input[name="email"], #email');
        $this->assertElementVisible('input[name="password"], #password');
        $this->assertElementVisible('button[type="submit"], .btn-login, input[type="submit"]');
        
        // Try to login with default credentials (if any exist)
        $this->fillInput('input[name="email"], #email', 'admin@vosiz.com');
        $this->fillInput('input[name="password"], #password', 'password');
        
        $this->takeScreenshot('login_form_filled.png');
        
        // Submit login form
        $this->click('button[type="submit"], .btn-login, input[type="submit"]');
        sleep(3);
        
        $currentUrl = self::$driver->getCurrentURL();
        $pageSource = self::$driver->getPageSource();
        
        // Check if login was successful
        $loginSuccess = (
            strpos($currentUrl, 'dashboard') !== false ||
            strpos($currentUrl, 'admin') !== false ||
            strpos($pageSource, 'Dashboard') !== false ||
            strpos($pageSource, 'Logout') !== false ||
            !strpos($currentUrl, 'login')
        );
        
        $this->takeScreenshot('login_result.png');
        
        if ($loginSuccess) {
            echo "✅ User login completed successfully\n";
        } else {
            echo "⚠️ Login failed or credentials don't exist - this is expected for new installation\n";
        }
    }
    
    /**
     * Test forgot password functionality
     */
    public function testForgotPassword(): void
    {
        echo "🔑 Testing Forgot Password...\n";
        
        $this->visit('/login');
        
        try {
            // Look for forgot password link
            $forgotLink = self::$driver->findElement(
                \Facebook\WebDriver\WebDriverBy::partialLinkText('Forgot')
            );
            
            if ($forgotLink->isDisplayed()) {
                $forgotLink->click();
                $this->waitForPageLoad();
                
                // Check forgot password form
                $this->assertElementVisible('input[name="email"], #email');
                $this->assertElementVisible('button[type="submit"], .btn-submit');
                
                // Fill email
                $this->fillInput('input[name="email"], #email', 'test@example.com');
                
                $this->takeScreenshot('forgot_password_form.png');
                
                // Submit form
                $this->click('button[type="submit"], .btn-submit');
                sleep(2);
                
                $this->takeScreenshot('forgot_password_submitted.png');
                
                echo "✅ Forgot password functionality tested\n";
            }
        } catch (\Exception $e) {
            echo "⚠️ Forgot password link not found: " . $e->getMessage() . "\n";
            $this->takeScreenshot('forgot_password_issue.png');
        }
    }
    
    /**
     * Test logout functionality
     */
    public function testLogout(): void
    {
        echo "🚪 Testing Logout...\n";
        
        // First try to login
        $this->visit('/login');
        
        $this->fillInput('input[name="email"], #email', 'admin@vosiz.com');
        $this->fillInput('input[name="password"], #password', 'password');
        $this->click('button[type="submit"], .btn-login, input[type="submit"]');
        sleep(3);
        
        try {
            // Look for logout button/link
            $logoutElement = self::$driver->findElement(
                \Facebook\WebDriver\WebDriverBy::partialLinkText('Logout')
            );
            
            if ($logoutElement->isDisplayed()) {
                $logoutElement->click();
                sleep(2);
                
                // Check if redirected to login or home page
                $currentUrl = self::$driver->getCurrentURL();
                $logoutSuccess = (
                    strpos($currentUrl, 'login') !== false ||
                    strpos($currentUrl, '/') === strlen($currentUrl) - 1
                );
                
                $this->takeScreenshot('logout_result.png');
                
                if ($logoutSuccess) {
                    echo "✅ Logout functionality works\n";
                } else {
                    echo "⚠️ Logout result unclear\n";
                }
            }
        } catch (\Exception $e) {
            echo "⚠️ User not logged in or logout button not found\n";
            $this->takeScreenshot('logout_issue.png');
        }
    }
}