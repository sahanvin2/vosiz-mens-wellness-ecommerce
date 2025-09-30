<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\MongoProduct;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductManagement extends Component
{
    use WithFileUploads, WithPagination;

    public $showModal = false;
    public $editingProduct = null;
    
    // Product form fields
    public $name = '';
    public $description = '';
    public $short_description = '';
    public $price = '';
    public $sale_price = '';
    public $category_id = '';
    public $sku = '';
    public $stock_quantity = '';
    public $weight = '';
    public $is_active = true;
    public $is_featured = false;
    public $tags = '';
    public $features = '';
    public $specifications = '';
    public $ingredients = '';
    public $usage_instructions = '';
    public $meta_title = '';
    public $meta_description = '';
    
    // File uploads
    public $images = [];
    public $video_file = null;
    public $introduction_video = null;
    
    // Search and filters
    public $search = '';
    public $categoryFilter = '';
    public $statusFilter = '';

    protected $rules = [
        'name' => 'required|min:3|max:255',
        'description' => 'required|min:10',
        'short_description' => 'required|min:10|max:500',
        'price' => 'required|numeric|min:0',
        'sale_price' => 'nullable|numeric|min:0',
        'category_id' => 'required|exists:categories,id',
        'sku' => 'required|unique:products,sku',
        'stock_quantity' => 'required|integer|min:0',
        'weight' => 'nullable|numeric|min:0',
        'images.*' => 'nullable|image|max:2048',
        'video_file' => 'nullable|mimes:mp4,avi,mov|max:51200', // 50MB max
        'introduction_video' => 'nullable|mimes:mp4,avi,mov|max:51200',
    ];

    public function mount()
    {
        $this->resetForm();
    }

    public function render()
    {
        $categories = Category::all();
        
        $productsQuery = MongoProduct::query();
        
        if ($this->search) {
            $productsQuery->search($this->search);
        }
        
        if ($this->categoryFilter) {
            $productsQuery->where('category_id', $this->categoryFilter);
        }
        
        if ($this->statusFilter !== '') {
            $productsQuery->where('is_active', $this->statusFilter == '1');
        }
        
        $products = $productsQuery->orderBy('updated_at', 'desc')->paginate(12);

        return view('livewire.product-management', compact('products', 'categories'));
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editingProduct = null;
        $this->name = '';
        $this->description = '';
        $this->short_description = '';
        $this->price = '';
        $this->sale_price = '';
        $this->category_id = '';
        $this->sku = '';
        $this->stock_quantity = '';
        $this->weight = '';
        $this->is_active = true;
        $this->is_featured = false;
        $this->tags = '';
        $this->features = '';
        $this->specifications = '';
        $this->ingredients = '';
        $this->usage_instructions = '';
        $this->meta_title = '';
        $this->meta_description = '';
        $this->images = [];
        $this->video_file = null;
        $this->introduction_video = null;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'description' => $this->description,
            'short_description' => $this->short_description,
            'price' => floatval($this->price),
            'sale_price' => $this->sale_price ? floatval($this->sale_price) : null,
            'category_id' => $this->category_id,
            'category_name' => Category::find($this->category_id)->name,
            'sku' => $this->sku,
            'stock_quantity' => intval($this->stock_quantity),
            'weight' => $this->weight ? floatval($this->weight) : null,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'tags' => $this->tags ? explode(',', $this->tags) : [],
            'features' => $this->features ? explode("\n", $this->features) : [],
            'specifications' => $this->specifications ? explode("\n", $this->specifications) : [],
            'ingredients' => $this->ingredients ? explode("\n", $this->ingredients) : [],
            'usage_instructions' => $this->usage_instructions,
            'meta_title' => $this->meta_title ?: $this->name,
            'meta_description' => $this->meta_description ?: $this->short_description,
        ];

        // Handle discount calculation
        if ($data['sale_price'] && $data['price'] > $data['sale_price']) {
            $data['discount_percentage'] = round((($data['price'] - $data['sale_price']) / $data['price']) * 100);
        }

        // Handle image uploads
        if ($this->images) {
            $imagePaths = [];
            foreach ($this->images as $image) {
                $path = $image->store('products', 'public');
                $imagePaths[] = $path;
            }
            $data['images'] = $imagePaths;
        }

        // Handle video uploads
        if ($this->video_file) {
            $data['video_url'] = $this->video_file->store('videos/products', 'public');
        }

        if ($this->introduction_video) {
            $data['introduction_video'] = $this->introduction_video->store('videos/intros', 'public');
        }

        MongoProduct::create($data);

        $this->dispatch('productSaved');
        $this->closeModal();
        
        session()->flash('message', 'Product created successfully!');
    }

    public function edit($productId)
    {
        $product = MongoProduct::findOrFail($productId);
        
        $this->editingProduct = $productId;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->short_description = $product->short_description;
        $this->price = $product->price;
        $this->sale_price = $product->sale_price;
        $this->category_id = $product->category_id;
        $this->sku = $product->sku;
        $this->stock_quantity = $product->stock_quantity;
        $this->weight = $product->weight;
        $this->is_active = $product->is_active;
        $this->is_featured = $product->is_featured;
        $this->tags = is_array($product->tags) ? implode(',', $product->tags) : '';
        $this->features = is_array($product->features) ? implode("\n", $product->features) : '';
        $this->specifications = is_array($product->specifications) ? implode("\n", $product->specifications) : '';
        $this->ingredients = is_array($product->ingredients) ? implode("\n", $product->ingredients) : '';
        $this->usage_instructions = $product->usage_instructions;
        $this->meta_title = $product->meta_title;
        $this->meta_description = $product->meta_description;
        
        $this->openModal();
    }

    public function delete($productId)
    {
        $product = MongoProduct::findOrFail($productId);
        
        // Delete associated files
        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        
        if ($product->video_url) {
            Storage::disk('public')->delete($product->video_url);
        }
        
        if ($product->introduction_video) {
            Storage::disk('public')->delete($product->introduction_video);
        }
        
        $product->delete();
        
        session()->flash('message', 'Product deleted successfully!');
    }

    public function toggleStatus($productId)
    {
        $product = MongoProduct::findOrFail($productId);
        $product->update(['is_active' => !$product->is_active]);
        
        session()->flash('message', 'Product status updated!');
    }

    public function toggleFeatured($productId)
    {
        $product = MongoProduct::findOrFail($productId);
        $product->update(['is_featured' => !$product->is_featured]);
        
        session()->flash('message', 'Product featured status updated!');
    }
}
