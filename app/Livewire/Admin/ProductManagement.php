<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\MongoDBProduct;
use App\Models\MongoCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductManagement extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $selectedCategory = '';
    public $statusFilter = 'all';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    // Form properties
    public $showModal = false;
    public $editMode = false;
    public $productId = null;
    
    public $name = '';
    public $slug = '';
    public $description = '';
    public $short_description = '';
    public $price = '';
    public $sale_price = '';
    public $category_id = '';
    public $category_name = '';
    public $sku = '';
    public $stock_quantity = 0;
    public $weight = '';
    public $features = '';
    public $tags = '';
    public $is_active = true;
    public $is_featured = false;
    public $images = [];
    public $existingImages = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'price' => 'required|numeric|min:0',
        'category_id' => 'required',
        'sku' => 'required|string',
        'stock_quantity' => 'required|integer|min:0',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->editMode = false;
    }

    public function openEditModal($productId)
    {
        $product = MongoDBProduct::findOrFail($productId);
        
        $this->productId = $product->_id;
        $this->name = $product->name;
        $this->slug = $product->slug;
        $this->description = $product->description;
        $this->short_description = $product->short_description;
        $this->price = $product->price;
        $this->sale_price = $product->sale_price;
        $this->category_id = $product->category_id;
        $this->sku = $product->sku;
        $this->stock_quantity = $product->stock_quantity;
        $this->weight = $product->weight;
        $this->features = is_array($product->features) ? implode("\n", $product->features) : $product->features;
        $this->tags = is_array($product->tags) ? implode(', ', $product->tags) : $product->tags;
        $this->is_active = ($product->status === 'active');
        $this->is_featured = $product->is_featured;
        $this->existingImages = $product->images ?? [];
        
        $this->showModal = true;
        $this->editMode = true;
    }

    public function save()
    {
        $this->validate();

        // Generate slug
        if (!$this->slug) {
            $this->slug = Str::slug($this->name);
        }

        // Handle features and tags
        $features = $this->features ? array_filter(explode("\n", $this->features)) : [];
        $tags = $this->tags ? array_filter(explode(',', $this->tags)) : [];

        // Handle image uploads
        $imagesPaths = $this->existingImages;
        if ($this->images) {
            foreach ($this->images as $image) {
                $path = $image->store('products', 'public');
                $imagesPaths[] = $path;
            }
        }

        // Get category name
        $category = MongoCategory::find($this->category_id);
        $this->category_name = $category ? $category->name : '';

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'price' => (float) $this->price,
            'sale_price' => $this->sale_price ? (float) $this->sale_price : null,
            'discount_percentage' => $this->sale_price ? round((($this->price - $this->sale_price) / $this->price) * 100) : 0,
            'category_id' => $this->category_id,
            'category_name' => $this->category_name,
            'sku' => $this->sku,
            'stock_quantity' => (int) $this->stock_quantity,
            'weight' => $this->weight ? (float) $this->weight : null,
            'features' => $features,
            'tags' => $tags,
            'status' => $this->is_active ? 'active' : 'inactive',
            'is_featured' => $this->is_featured,
            'images' => $imagesPaths,
        ];

        if ($this->editMode) {
            $product = MongoDBProduct::findOrFail($this->productId);
            $product->update($data);
            session()->flash('success', 'Product updated successfully!');
        } else {
            MongoDBProduct::create($data);
            session()->flash('success', 'Product created successfully!');
        }

        $this->resetForm();
        $this->showModal = false;
    }

    public function deleteProduct($productId)
    {
        $product = MongoDBProduct::findOrFail($productId);
        
        // Delete associated images
        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        
        $product->delete();
        session()->flash('success', 'Product deleted successfully!');
    }

    public function toggleStatus($productId)
    {
        $product = MongoDBProduct::findOrFail($productId);
        $newStatus = ($product->status === 'active') ? 'inactive' : 'active';
        $product->update(['status' => $newStatus]);
        
        $status = ($newStatus === 'active') ? 'activated' : 'deactivated';
        session()->flash('success', "Product {$status} successfully!");
    }

    public function toggleFeatured($productId)
    {
        $product = MongoDBProduct::findOrFail($productId);
        $product->update(['is_featured' => !$product->is_featured]);
        
        $status = $product->is_featured ? 'marked as featured' : 'removed from featured';
        session()->flash('success', "Product {$status} successfully!");
    }

    private function resetForm()
    {
        $this->reset([
            'productId', 'name', 'slug', 'description', 'short_description',
            'price', 'sale_price', 'category_id', 'category_name', 'sku',
            'stock_quantity', 'weight', 'features', 'tags', 'is_active',
            'is_featured', 'images', 'existingImages'
        ]);
        $this->is_active = true;
        $this->is_featured = false;
    }

    public function render()
    {
        $query = MongoDBProduct::query();

        // Apply filters
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('sku', 'like', '%' . $this->search . '%');
        }

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $products = $query->paginate(10);
        $categories = MongoCategory::where('status', true)->get();

        return view('livewire.admin.product-management', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}