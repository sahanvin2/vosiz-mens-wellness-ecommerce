<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Exception\WebDriverException;

abstract class SeleniumTestCase extends PHPUnitTestCase
{
    protected static $driver;
    protected $baseUrl;
    protected $wait;
    
    public static function setUpBeforeClass(): void
    {
        // Start Selenium WebDriver
        try {
            $seleniumUrl = $_ENV['SELENIUM_DRIVER_URL'] ?? 'http://localhost:4444';
            $capabilities = DesiredCapabilities::chrome();
            
            // Chrome options for headless testing
            $capabilities->setCapability('goog:chromeOptions', [
                'args' => [
                    '--headless',
                    '--no-sandbox',
                    '--disable-dev-shm-usage',
                    '--disable-gpu',
                    '--window-size=1920,1080'
                ]
            ]);
            
            self::$driver = RemoteWebDriver::create($seleniumUrl, $capabilities);
            self::$driver->manage()->window()->maximize();
            
            echo "✅ WebDriver initialized successfully\n";
            
        } catch (WebDriverException $e) {
            echo "❌ Failed to initialize WebDriver: " . $e->getMessage() . "\n";
            echo "💡 Make sure Selenium Server is running on " . $seleniumUrl . "\n";
            throw $e;
        }
    }
    
    public static function tearDownAfterClass(): void
    {
        if (self::$driver) {
            self::$driver->quit();
            echo "🔒 WebDriver closed\n";
        }
    }
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->baseUrl = $_ENV['BASE_URL'] ?? 'http://localhost:8000';
        $this->wait = new WebDriverWait(self::$driver, 10);
    }
    
    protected function tearDown(): void
    {
        // Clear cookies and local storage between tests
        if (self::$driver) {
            self::$driver->manage()->deleteAllCookies();
        }
        parent::tearDown();
    }
    
    /**
     * Navigate to a specific URL
     */
    protected function visit(string $path = ''): void
    {
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/');
        self::$driver->get($url);
        $this->waitForPageLoad();
    }
    
    /**
     * Wait for page to load completely
     */
    protected function waitForPageLoad(): void
    {
        $this->wait->until(
            WebDriverExpectedCondition::jsCondition('return document.readyState === "complete"')
        );
    }
    
    /**
     * Wait for element to be visible
     */
    protected function waitForElement(string $selector, int $timeout = 10)
    {
        $wait = new WebDriverWait(self::$driver, $timeout);
        return $wait->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(
                WebDriverBy::cssSelector($selector)
            )
        );
    }
    
    /**
     * Wait for element to be clickable
     */
    protected function waitForClickable(string $selector, int $timeout = 10)
    {
        $wait = new WebDriverWait(self::$driver, $timeout);
        return $wait->until(
            WebDriverExpectedCondition::elementToBeClickable(
                WebDriverBy::cssSelector($selector)
            )
        );
    }
    
    /**
     * Take a screenshot for debugging
     */
    protected function takeScreenshot(string $filename = null): string
    {
        if (!$filename) {
            $filename = 'screenshot_' . date('Y-m-d_H-i-s') . '.png';
        }
        
        $screenshotPath = __DIR__ . '/screenshots/' . $filename;
        
        // Create screenshots directory if it doesn't exist
        $dir = dirname($screenshotPath);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        
        self::$driver->takeScreenshot($screenshotPath);
        echo "📸 Screenshot saved: $screenshotPath\n";
        
        return $screenshotPath;
    }
    
    /**
     * Assert that text is present on the page
     */
    protected function assertTextPresent(string $text): void
    {
        $pageSource = self::$driver->getPageSource();
        $this->assertStringContainsString($text, $pageSource, "Text '$text' not found on page");
    }
    
    /**
     * Assert that element is visible
     */
    protected function assertElementVisible(string $selector): void
    {
        $element = self::$driver->findElement(WebDriverBy::cssSelector($selector));
        $this->assertTrue($element->isDisplayed(), "Element '$selector' is not visible");
    }
    
    /**
     * Fill form input
     */
    protected function fillInput(string $selector, string $value): void
    {
        $element = $this->waitForElement($selector);
        $element->clear();
        $element->sendKeys($value);
    }
    
    /**
     * Click element
     */
    protected function click(string $selector): void
    {
        $element = $this->waitForClickable($selector);
        $element->click();
    }
    
    /**
     * Get element text
     */
    protected function getText(string $selector): string
    {
        $element = $this->waitForElement($selector);
        return $element->getText();
    }
}