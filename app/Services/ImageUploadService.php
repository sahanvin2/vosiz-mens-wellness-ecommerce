<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ImageUploadService
{
    /**
     * Upload product images to storage and return URLs
     */
    public function uploadProductImages(array $images, string $productSlug): array
    {
        $uploadedImages = [];
        
        foreach ($images as $index => $image) {
            if ($image instanceof UploadedFile) {
                $uploadedImages[] = $this->uploadSingleImage($image, $productSlug, $index);
            }
        }
        
        return $uploadedImages;
    }
    
    /**
     * Upload a single image
     */
    private function uploadSingleImage(UploadedFile $image, string $productSlug, int $index): string
    {
        // Generate unique filename
        $filename = $productSlug . '-' . ($index + 1) . '-' . Str::random(8) . '.' . $image->getClientOriginalExtension();
        
        // Resize and optimize image
        $processedImage = Image::make($image)
            ->resize(800, 800, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode('jpg', 85);
        
        // Store in public/images/products
        $path = 'images/products/' . $filename;
        Storage::disk('public')->put($path, $processedImage);
        
        return '/storage/' . $path;
    }
    
    /**
     * Delete product images
     */
    public function deleteProductImages(array $imagePaths): void
    {
        foreach ($imagePaths as $path) {
            $storagePath = str_replace('/storage/', '', $path);
            Storage::disk('public')->delete($storagePath);
        }
    }
    
    /**
     * Upload category image
     */
    public function uploadCategoryImage(UploadedFile $image, string $categorySlug): string
    {
        $filename = $categorySlug . '-' . Str::random(8) . '.' . $image->getClientOriginalExtension();
        
        $processedImage = Image::make($image)
            ->resize(400, 300, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode('jpg', 85);
        
        $path = 'images/categories/' . $filename;
        Storage::disk('public')->put($path, $processedImage);
        
        return '/storage/' . $path;
    }
}