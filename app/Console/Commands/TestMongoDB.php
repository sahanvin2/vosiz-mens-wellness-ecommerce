<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MongoProduct;
use App\Services\MongoDbService;

class TestMongoDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mongodb:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test MongoDB-style functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing MongoDB-Style Functionality...');
        $this->newLine();

        // Test 1: Create documents
        $this->info('1️⃣ Testing Document Creation:');
        $product1 = MongoProduct::insertOne([
            'name' => 'MongoDB Test Product 1',
            'slug' => 'mongodb-test-product-1',
            'description' => 'This is a test product for MongoDB functionality',
            'price' => 29.99,
            'sku' => 'MONGO-001',
            'category_id' => 1,
            'category_name' => 'Test Category',
            'is_active' => true,
            'stock_quantity' => 100,
        ]);
        $this->line("✅ Created product with ObjectId: {$product1->_id}");

        // Test 2: Find by ObjectId
        $this->info('2️⃣ Testing Find by ObjectId:');
        $found = MongoProduct::findByObjectId($product1->_id);
        $this->line("✅ Found product: {$found->name}");

        // Test 3: Text Search
        $this->info('3️⃣ Testing Text Search:');
        $searchResults = MongoProduct::textSearch('MongoDB')->get();
        $this->line("✅ Text search found {$searchResults->count()} products");

        // Test 4: Aggregation Pipeline
        $this->info('4️⃣ Testing Aggregation Pipeline:');
        $pipeline = [
            ['$match' => ['is_active' => true]],
            ['$sort' => ['price' => -1]],
            ['$limit' => 5]
        ];
        $aggregated = MongoProduct::aggregate($pipeline);
        $this->line("✅ Aggregation returned {$aggregated->count()} products");

        // Test 5: Helper Functions
        $this->info('5️⃣ Testing Helper Functions:');
        $objectId = object_id();
        $this->line("✅ Generated ObjectId: {$objectId}");

        $mongoFind = mongo_find(['is_active' => true]);
        $this->line("✅ mongo_find() returned {$mongoFind->count()} products");

        // Test 6: Service Class
        $this->info('6️⃣ Testing MongoDB Service:');
        $service = new MongoDbService();
        $stats = $service->getStats();
        $this->line("✅ Database stats:");
        foreach ($stats as $key => $value) {
            $this->line("   - {$key}: {$value}");
        }

        // Test 7: Document Operations
        $this->info('7️⃣ Testing Document Operations:');
        $document = $product1->toDocument();
        $this->line("✅ Document conversion successful");
        $this->line("   Document keys: " . implode(', ', array_keys($document)));

        // Test 8: Complex Query
        $this->info('8️⃣ Testing Complex MongoDB-style Query:');
        $complexResults = mongo_find([
            'price' => ['$gte' => 20, '$lte' => 50],
            'is_active' => true
        ]);
        $this->line("✅ Complex query returned {$complexResults->count()} products");

        $this->newLine();
        $this->info('🎉 All MongoDB-style tests completed successfully!');
        $this->newLine();
        $this->comment('💡 You can now use MongoDB-style operations in your Laravel app:');
        $this->line('   - MongoProduct::insertOne($data)');
        $this->line('   - MongoProduct::findByObjectId($id)');
        $this->line('   - MongoProduct::textSearch($term)');
        $this->line('   - MongoProduct::aggregate($pipeline)');
        $this->line('   - mongo_find($query)');
        $this->line('   - mongo_insert($document)');
        $this->newLine();
    }
}