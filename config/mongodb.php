<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MongoDB-Style Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the MongoDB-style service
    | that uses MySQL JSON fields to provide MongoDB-like functionality.
    |
    */

    'default_collection' => 'products',

    'collections' => [
        'products' => [
            'model' => App\Models\MongoProduct::class,
            'indexes' => [
                'name' => 'text',
                'category_id' => 1,
                'is_active' => 1,
                'price' => 1,
            ],
        ],
        'videos' => [
            'model' => App\Models\Video::class,
            'indexes' => [
                'title' => 'text',
                'type' => 1,
                'is_active' => 1,
            ],
        ],
    ],

    'features' => [
        'text_search' => true,
        'aggregation' => true,
        'json_queries' => true,
        'bulk_operations' => true,
    ],

    'limits' => [
        'max_document_size' => 16 * 1024 * 1024, // 16MB (MySQL JSON limit)
        'default_batch_size' => 100,
        'max_query_time' => 30, // seconds
    ],
];