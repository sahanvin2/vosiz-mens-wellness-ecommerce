<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables
if (file_exists(__DIR__ . '/../.env.testing')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/..', '.env.testing');
    $dotenv->load();
} elseif (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

// Create reports directory if it doesn't exist
$reportsDir = __DIR__ . '/reports';
if (!file_exists($reportsDir)) {
    mkdir($reportsDir, 0755, true);
}

// Set default test environment variables
if (!isset($_ENV['BASE_URL'])) {
    $_ENV['BASE_URL'] = 'http://localhost:8000';
}

if (!isset($_ENV['SELENIUM_DRIVER_URL'])) {
    $_ENV['SELENIUM_DRIVER_URL'] = 'http://localhost:4444';
}

echo "🚀 Test Environment Initialized\n";
echo "📍 Base URL: " . $_ENV['BASE_URL'] . "\n";
echo "🔗 Selenium URL: " . $_ENV['SELENIUM_DRIVER_URL'] . "\n\n";