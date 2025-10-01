<?php

namespace App\Services;

use MongoDB\Client;
use MongoDB\Database;
use MongoDB\Collection;
use Illuminate\Support\Facades\Log;

class MongoDBAtlasService
{
    private $client;
    private $database;
    
    public function __construct()
    {
        $this->connect();
    }
    
    /**
     * Establish connection to MongoDB Atlas
     * 
     * @return void
     */
    private function connect()
    {
        try {
            $connectionString = config('database.connections.mongodb.dsn');
            
            // Log connection attempt
            Log::info('VOSIZ MongoDB Atlas: Attempting connection', [
                'host' => parse_url($connectionString, PHP_URL_HOST),
                'database' => config('database.connections.mongodb.database')
            ]);
            
            $this->client = new Client($connectionString, [
                'typeMap' => [
                    'array' => 'array',
                    'document' => 'array',
                    'root' => 'array',
                ]
            ]);
            
            $this->database = $this->client->selectDatabase(config('database.connections.mongodb.database'));
            
            // Test the connection
            if (!$this->testConnection()) {
                throw new \Exception('MongoDB Atlas ping test failed');
            }
            
            Log::info('VOSIZ MongoDB Atlas: Connection established successfully');
            
        } catch (\Exception $e) {
            Log::error('VOSIZ MongoDB Atlas connection failed: ' . $e->getMessage(), [
                'dsn_configured' => !empty($connectionString),
                'error_type' => get_class($e)
            ]);
            throw new \Exception('Failed to connect to MongoDB Atlas: ' . $e->getMessage());
        }
    }
    
    /**
     * Test MongoDB Atlas connection
     * 
     * @return bool
     */
    public function testConnection()
    {
        try {
            // Ping the database
            $result = $this->database->command(['ping' => 1]);
            $response = $result->toArray()[0] ?? null;
            return isset($response['ok']) && $response['ok'] == 1;
        } catch (\Exception $e) {
            Log::error('MongoDB Atlas ping failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get MongoDB database instance
     * 
     * @return Database
     */
    public function getDatabase()
    {
        return $this->database;
    }
    
    /**
     * Get specific collection
     * 
     * @param string $collectionName
     * @return Collection
     */
    public function getCollection($collectionName)
    {
        return $this->database->selectCollection($collectionName);
    }
    
    /**
     * Get client instance
     * 
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }
    
    /**
     * Create collection with schema validation
     * 
     * @param string $collectionName
     * @param array $validationRules
     * @return bool
     */
    public function createCollection($collectionName, $validationRules = [])
    {
        try {
            $options = [];
            
            if (!empty($validationRules)) {
                $options['validator'] = $validationRules;
                $options['validationLevel'] = 'strict';
                $options['validationAction'] = 'error';
            }
            
            $this->database->createCollection($collectionName, $options);
            
            Log::info("Collection '{$collectionName}' created successfully");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to create collection '{$collectionName}': " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create indexes for better performance
     * 
     * @param string $collectionName
     * @param array $indexes
     * @return bool
     */
    public function createIndexes($collectionName, $indexes)
    {
        try {
            $collection = $this->getCollection($collectionName);
            
            foreach ($indexes as $index) {
                $collection->createIndex($index['keys'], $index['options'] ?? []);
            }
            
            Log::info("Indexes created for collection '{$collectionName}'");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to create indexes for '{$collectionName}': " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get connection statistics
     * 
     * @return array
     */
    public function getConnectionStats()
    {
        try {
            $stats = $this->database->command(['dbStats' => 1]);
            $statsArray = $stats->toArray()[0] ?? null;
            
            if (!$statsArray) {
                return [];
            }
            
            return [
                'database' => $statsArray['db'] ?? 'N/A',
                'collections' => $statsArray['collections'] ?? 0,
                'objects' => $statsArray['objects'] ?? 0,
                'avgObjSize' => $statsArray['avgObjSize'] ?? 0,
                'dataSize' => $statsArray['dataSize'] ?? 0,
                'storageSize' => $statsArray['storageSize'] ?? 0,
                'indexes' => $statsArray['indexes'] ?? 0,
                'indexSize' => $statsArray['indexSize'] ?? 0
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to get MongoDB stats: ' . $e->getMessage());
            return [];
        }
    }
}