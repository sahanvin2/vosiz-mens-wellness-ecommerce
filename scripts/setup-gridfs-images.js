// GridFS Image Storage Setup for Vosiz MongoDB Atlas
// This script demonstrates how to store images using GridFS

// Connect to vosiz_products database
use vosiz_products

// Create GridFS bucket for product images
print("🖼️  Setting up GridFS for image storage...")

// GridFS collections will be created automatically when you upload files
// fs.files - stores file metadata
// fs.chunks - stores file data in chunks

// Example: How to upload an image using GridFS (this would be done from your application)
print("📋 GridFS Collections Structure:")
print("- fs.files: Image metadata (filename, contentType, length, uploadDate, etc.)")
print("- fs.chunks: Image data chunks (each chunk is max 255KB)")

// Sample metadata structure for images
db.image_metadata.insertMany([
  {
    product_slug: "premium-beard-oil",
    image_type: "main",
    gridfs_file_id: null, // Will be populated when image is uploaded via GridFS
    filename: "beard-oil-main.jpg",
    alt_text: "Premium Beard Oil - Natural ingredients for healthy beard",
    display_order: 1,
    is_active: true,
    created_at: new Date()
  },
  {
    product_slug: "premium-beard-oil",
    image_type: "gallery",
    gridfs_file_id: null,
    filename: "beard-oil-ingredients.jpg", 
    alt_text: "Natural ingredients used in Premium Beard Oil",
    display_order: 2,
    is_active: true,
    created_at: new Date()
  },
  {
    product_slug: "activated-charcoal-face-wash",
    image_type: "main",
    gridfs_file_id: null,
    filename: "charcoal-wash-main.jpg",
    alt_text: "Activated Charcoal Face Wash for deep cleansing",
    display_order: 1,
    is_active: true,
    created_at: new Date()
  }
])

print("✅ Image metadata structure created")
print("💡 To upload actual images, use Laravel's GridFS implementation")

// Create indexes for image metadata
db.image_metadata.createIndex({ "product_slug": 1, "image_type": 1 })
db.image_metadata.createIndex({ "gridfs_file_id": 1 })
db.image_metadata.createIndex({ "is_active": 1, "display_order": 1 })

print("✅ Image metadata indexes created")
print("\n🎯 Next Steps:")
print("1. Install Laravel MongoDB GridFS package")
print("2. Create image upload controller")
print("3. Implement image serving endpoint")