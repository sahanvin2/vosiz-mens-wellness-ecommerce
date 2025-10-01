# VOSIZ Developer Onboarding Guide

## 🚀 Quick Start for New Developers

### Prerequisites
- PHP 8.1+ with extensions: mysqli, pdo_mysql, mongodb
- Composer
- Node.js & npm
- MySQL Server
- MongoDB Atlas account (provided)

### 1. Clone & Setup
```bash
git clone https://github.com/sahanvin2/vosiz-mens-wellness-ecommerce.git
cd vosiz-mens-wellness-ecommerce
composer install
npm install
cp .env.example .env
php artisan key:generate
```

### 2. Database Configuration
**MySQL Setup** (`.env`):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=vosiz_ecommerce
DB_USERNAME=root
DB_PASSWORD=
```

**MongoDB Atlas** (Already configured):
```env
MONGO_DB_HOST=cluster0.2m8hhzb.mongodb.net
MONGO_DB_DATABASE=vosiz_products
MONGO_DB_USERNAME=sahannawarathne2004_db_user
MONGO_DB_PASSWORD=j6caFrJSLCuJ2uP6
```

### 3. Run Migrations & Seed Data
```bash
php artisan migrate
php artisan db:seed
php artisan mongo:populate-atlas
```

### 4. Start Development
```bash
php artisan serve
npm run dev
```

---

## 📋 CRITERIA IMPLEMENTATION DETAILS

### ✅ Laravel 12 Framework
**Location**: Root project structure  
**Evidence**: `composer.json` line 8  
**How implemented**: 
- Used Laravel 12.31.1 as base framework
- All modern Laravel features included
- Follows Laravel conventions and best practices

### ✅ SQL Database Connection  
**Location**: `config/database.php` lines 46-58  
**How implemented**:
- MySQL connection configured for primary data
- Handles users, orders, categories, sessions
- Uses Laravel's database abstraction layer

**Key Files**:
- `config/database.php` - Database configuration
- `.env` - Environment variables
- `database/migrations/` - Database schema

### ✅ Livewire External Library
**Location**: `resources/views/livewire/`  
**How implemented**:
- Installed via Composer: `"livewire/livewire": "^3.0"`
- Created interactive admin components
- Real-time product management interface

**Key Components**:
- `resources/views/livewire/admin/product-management.blade.php` - Admin product CRUD
- `resources/views/livewire/video-management.blade.php` - Video management
- Real-time search and filtering functionality

**Code Example**:
```php
// Livewire component with real-time search
<input wire:model.live="search" type="text" placeholder="Search products...">
<button wire:click="createProduct">Add Product</button>
```

### ✅ Laravel Eloquent Models
**Location**: `app/Models/`  
**How implemented**:
- Created multiple Eloquent models for MySQL data
- Implemented relationships between models
- Used proper casting and fillable attributes

**Key Models**:
- `app/Models/User.php` - User authentication and relationships
- `app/Models/Category.php` - Product categories
- `app/Models/Order.php` - Order management
- `app/Models/MongoDBProduct.php` - MongoDB products

**Relationships Example**:
```php
// User.php - Eloquent relationships
public function orders()
{
    return $this->hasMany(Order::class);
}

public function cartItems()
{
    return $this->hasMany(CartItem::class);
}
```

### ✅ Laravel Jetstream Authentication
**Location**: `config/jetstream.php` and auth views  
**How implemented**:
- Installed Laravel Jetstream with Livewire stack
- Configured authentication features
- Protected routes with middleware

**Key Features**:
- Login/Register functionality
- Profile management with photos
- API token management
- Team management capabilities

**Protected Routes Example**:
```php
// routes/web.php - Jetstream protected routes
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
```

### ✅ Laravel Sanctum API Authentication
**Location**: `config/sanctum.php` and API routes  
**How implemented**:
- Configured Sanctum for API authentication
- Created protected API endpoints
- Implemented token-based authentication

**API Routes Example**:
```php
// Sanctum protected API routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/cart/add', [CartController::class, 'apiAdd']);
    Route::get('/profile', [UserController::class, 'apiProfile']);
});
```

**Token Management**:
- Admin can create API tokens
- Token-based access control
- API rate limiting configured

### ✅ Security Documentation & Implementation
**Location**: Multiple security layers throughout app  
**How implemented**:

**1. Middleware Protection**:
```php
// app/Http/Middleware/AdminMiddleware.php
public function handle(Request $request, Closure $next)
{
    if (!auth()->check() || auth()->user()->role !== 'admin') {
        abort(403, 'Access denied');
    }
    return $next($request);
}
```

**2. CSRF Protection**:
```php
// All forms include CSRF tokens
<form method="POST">
    @csrf
    <!-- form fields -->
</form>
```

**3. Input Validation**:
```php
// AdminProductController.php - Request validation
$validated = $request->validate([
    'name' => 'required|string|max:255',
    'price' => 'required|numeric|min:0',
    'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
]);
```

### ✅ MongoDB NoSQL Database for API
**Location**: `app/Models/MongoDBProduct.php` and MongoDB config  
**How implemented**:
- Connected to MongoDB Atlas cloud service
- Created MongoDB Eloquent model
- API endpoints for product data

**MongoDB Model Example**:
```php
// app/Models/MongoDBProduct.php
class MongoDBProduct extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'products';
    
    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'images' => 'array',
    ];
}
```

**API Implementation**:
```php
// MongoDB API endpoint
Route::get('/api/products', function (Request $request) {
    $products = MongoDBProduct::where('is_active', true)
        ->limit($request->get('limit', 12))
        ->get();
    
    return response()->json(['success' => true, 'products' => $products]);
});
```

### ✅ Hosting Service Provider Ready
**Location**: `deployment/` directory  
**How implemented**:
- Created Docker configuration
- AWS Elastic Beanstalk setup
- Environment-specific configurations

**Deployment Files**:
- `deployment/docker/docker-compose.yml` - Docker setup
- `deployment/elastic-beanstalk/` - AWS configuration
- `deployment/scripts/` - Deployment automation

---

## 🔍 Code Location Reference

### Controllers
- **Admin Product CRUD**: `app/Http/Controllers/Admin/AdminProductController.php`
- **Customer Products**: `app/Http/Controllers/ProductController.php`
- **Admin Dashboard**: `app/Http/Controllers/AdminDashboardController.php`

### Models & Database
- **MySQL Models**: `app/Models/User.php`, `app/Models/Category.php`
- **MongoDB Model**: `app/Models/MongoDBProduct.php`
- **Migrations**: `database/migrations/`
- **Seeders**: `database/seeders/`

### Authentication & Security
- **Jetstream Config**: `config/jetstream.php`
- **Sanctum Config**: `config/sanctum.php`
- **Admin Middleware**: `app/Http/Middleware/AdminMiddleware.php`
- **Auth Views**: `resources/views/auth/`

### Frontend & Livewire
- **Livewire Components**: `resources/views/livewire/`
- **Product Views**: `resources/views/products/`
- **Admin Views**: `resources/views/admin/`

### API & Routes
- **Web Routes**: `routes/web.php`
- **API Routes**: `routes/api.php`
- **MongoDB API**: `routes/web.php` lines 50-85

### Configuration
- **Database**: `config/database.php`
- **Environment**: `.env`
- **Services**: `app/Services/`

### Testing & Scripts
- **Setup Scripts**: `scripts/populate-mongodb.php`
- **Test Scripts**: `scripts/test-product-fix.php`
- **Console Commands**: `app/Console/Commands/`

---

## 🎯 Business Logic Implementation

### Dual Database Architecture
- **MySQL**: Core business data (users, orders, sessions)
- **MongoDB**: Product catalog, reviews, analytics
- **Benefit**: Optimized for different data types and access patterns

### Role-Based Access Control
- **Admin**: Full system access, product management
- **User**: Shopping, cart, orders
- **Supplier**: Product submission, order fulfillment

### Real-time Features with Livewire
- Live product search and filtering
- Real-time inventory updates
- Dynamic admin interface

### API-First Approach
- RESTful API with Sanctum authentication
- Mobile app ready endpoints
- Third-party integration capabilities

---

## 📱 Features Summary

### ✅ Implemented Features
- User authentication (Jetstream)
- Admin product management (CRUD)
- Customer product browsing
- Shopping cart functionality
- Order management
- API endpoints (Sanctum)
- Real-time admin interface (Livewire)
- Dual database architecture
- Image upload and management
- Security implementation

### 🔄 Ready for Extension
- Payment gateway integration
- Email notifications
- Advanced analytics
- Mobile app API
- Third-party integrations

---

**This documentation provides comprehensive coverage of all criteria implementation with exact code locations and examples for easy reference by new developers joining the project.**