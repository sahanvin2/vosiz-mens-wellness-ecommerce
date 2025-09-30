<?php

namespace App\Services;

use App\Models\MongoProduct;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MongoDbService
{
    /**
     * Create a new document in the collection
     */
    public function create(array $data): MongoProduct
    {
        // Generate MongoDB-style ID if not provided
        if (!isset($data['_id'])) {
            $data['_id'] = $this->generateObjectId();
        }

        // Convert array data to JSON for storage
        $document = new MongoProduct();
        $document->fill($data);
        $document->document_data = json_encode($data);
        $document->save();

        return $document;
    }

    /**
     * Find a document by ID
     */
    public function find(string $id): ?MongoProduct
    {
        return MongoProduct::where('_id', $id)->first() 
            ?? MongoProduct::find($id);
    }

    /**
     * Find documents by criteria
     */
    public function findWhere(array $criteria): Collection
    {
        $query = MongoProduct::query();

        foreach ($criteria as $field => $value) {
            if (in_array($field, ['name', 'category_name', 'sku'])) {
                // Direct field search
                $query->where($field, 'like', "%{$value}%");
            } else {
                // JSON field search
                $query->whereJsonContains("document_data->{$field}", $value);
            }
        }

        return $query->get();
    }

    /**
     * Update a document
     */
    public function update(string $id, array $data): bool
    {
        $document = $this->find($id);
        if (!$document) {
            return false;
        }

        // Merge with existing data
        $existingData = json_decode($document->document_data ?? '{}', true);
        $mergedData = array_merge($existingData, $data);

        $document->fill($data);
        $document->document_data = json_encode($mergedData);
        
        return $document->save();
    }

    /**
     * Delete a document
     */
    public function delete(string $id): bool
    {
        $document = $this->find($id);
        if (!$document) {
            return false;
        }

        return $document->delete();
    }

    /**
     * Search documents with advanced queries
     */
    public function search(array $query = []): Collection
    {
        $builder = MongoProduct::query();

        // Text search
        if (isset($query['$text'])) {
            $searchTerm = $query['$text']['$search'] ?? '';
            $builder->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('sku', 'like', "%{$searchTerm}%");
            });
        }

        // Range queries
        if (isset($query['price'])) {
            $priceQuery = $query['price'];
            if (isset($priceQuery['$gte'])) {
                $builder->where('price', '>=', $priceQuery['$gte']);
            }
            if (isset($priceQuery['$lte'])) {
                $builder->where('price', '<=', $priceQuery['$lte']);
            }
        }

        // Category filter
        if (isset($query['category_id'])) {
            $builder->where('category_id', $query['category_id']);
        }

        // Active filter
        if (isset($query['is_active'])) {
            $builder->where('is_active', $query['is_active']);
        }

        // Sorting
        if (isset($query['$sort'])) {
            foreach ($query['$sort'] as $field => $direction) {
                $builder->orderBy($field, $direction === 1 ? 'asc' : 'desc');
            }
        }

        // Limit
        if (isset($query['$limit'])) {
            $builder->limit($query['$limit']);
        }

        return $builder->get();
    }

    /**
     * Aggregate data (like MongoDB aggregation pipeline)
     */
    public function aggregate(array $pipeline): Collection
    {
        $query = MongoProduct::query();

        foreach ($pipeline as $stage) {
            if (isset($stage['$match'])) {
                // Match stage
                foreach ($stage['$match'] as $field => $value) {
                    $query->where($field, $value);
                }
            }

            if (isset($stage['$group'])) {
                // Group stage (simplified)
                $groupBy = $stage['$group']['_id'] ?? null;
                if ($groupBy) {
                    $query->groupBy($groupBy);
                }
            }

            if (isset($stage['$sort'])) {
                // Sort stage
                foreach ($stage['$sort'] as $field => $direction) {
                    $query->orderBy($field, $direction === 1 ? 'asc' : 'desc');
                }
            }

            if (isset($stage['$limit'])) {
                // Limit stage
                $query->limit($stage['$limit']);
            }
        }

        return $query->get();
    }

    /**
     * Get collection statistics
     */
    public function getStats(): array
    {
        return [
            'total_documents' => MongoProduct::count(),
            'active_documents' => MongoProduct::where('is_active', true)->count(),
            'featured_documents' => MongoProduct::where('is_featured', true)->count(),
            'categories_count' => MongoProduct::distinct('category_id')->count(),
            'average_price' => MongoProduct::avg('price'),
            'total_stock' => MongoProduct::sum('stock_quantity'),
        ];
    }

    /**
     * Generate MongoDB-style ObjectId
     */
    private function generateObjectId(): string
    {
        return sprintf('%04x%04x%04x%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff)
        );
    }

    /**
     * Create indexes (simplified for MySQL JSON)
     */
    public function createIndex(array $fields): bool
    {
        // In a real implementation, this would create database indexes
        // For now, we'll just return true as MySQL handles JSON indexing automatically
        return true;
    }

    /**
     * Bulk insert documents
     */
    public function insertMany(array $documents): array
    {
        $results = [];
        foreach ($documents as $doc) {
            $results[] = $this->create($doc);
        }
        return $results;
    }

    /**
     * Count documents matching criteria
     */
    public function count(array $criteria = []): int
    {
        $query = MongoProduct::query();

        foreach ($criteria as $field => $value) {
            $query->where($field, $value);
        }

        return $query->count();
    }

    /**
     * Get distinct values for a field
     */
    public function distinct(string $field): Collection
    {
        return MongoProduct::distinct($field)->pluck($field);
    }
}