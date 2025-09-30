// Initialize Vosiz MongoDB Database
db = db.getSiblingDB('vosiz_mongo');

// Create collections with validation
db.createCollection('products', {
  validator: {
    $jsonSchema: {
      bsonType: 'object',
      required: ['name', 'price', 'status'],
      properties: {
        name: {
          bsonType: 'string',
          description: 'Product name is required and must be a string'
        },
        price: {
          bsonType: 'number',
          minimum: 0,
          description: 'Price must be a positive number'
        },
        status: {
          bsonType: 'string',
          enum: ['active', 'inactive'],
          description: 'Status must be either active or inactive'
        }
      }
    }
  }
});

// Create indexes for better performance
db.products.createIndex({ "name": 1 });
db.products.createIndex({ "category_name": 1 });
db.products.createIndex({ "status": 1 });
db.products.createIndex({ "is_featured": 1 });
db.products.createIndex({ "created_at": -1 });

// Insert sample data if needed
db.products.insertOne({
  name: "Premium Face Moisturizer",
  slug: "premium-face-moisturizer",
  description: "Advanced hydrating moisturizer for men's skin",
  price: 29.99,
  category_name: "Skincare",
  status: "active",
  is_featured: true,
  created_at: new Date()
});

print("Vosiz MongoDB Database initialized successfully!");