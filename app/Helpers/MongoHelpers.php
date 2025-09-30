<?php

if (!function_exists('mongo_collection')) {
    /**
     * Get a MongoDB-style collection instance
     */
    function mongo_collection(string $collection = 'products'): App\Services\MongoDbService
    {
        return new App\Services\MongoDbService();
    }
}

if (!function_exists('mongo_find')) {
    /**
     * Find documents in MongoDB style
     */
    function mongo_find(array $query = [], array $options = []): Illuminate\Support\Collection
    {
        $service = new App\Services\MongoDbService();
        return $service->search($query);
    }
}

if (!function_exists('mongo_insert')) {
    /**
     * Insert a document in MongoDB style
     */
    function mongo_insert(array $document): App\Models\MongoProduct
    {
        $service = new App\Services\MongoDbService();
        return $service->create($document);
    }
}

if (!function_exists('mongo_aggregate')) {
    /**
     * Perform MongoDB-style aggregation
     */
    function mongo_aggregate(array $pipeline): Illuminate\Support\Collection
    {
        $service = new App\Services\MongoDbService();
        return $service->aggregate($pipeline);
    }
}

if (!function_exists('object_id')) {
    /**
     * Generate a MongoDB-style ObjectId
     */
    function object_id(): string
    {
        return sprintf('%04x%04x%04x%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff)
        );
    }
}