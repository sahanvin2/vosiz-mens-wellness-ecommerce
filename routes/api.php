<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductApiController;
use App\Models\MongoDBProduct;
use App\Models\Category;

// Public API endpoints (no authentication required)
Route::get('/products/featured', function () {
    return MongoDBProduct::where('is_featured', true)
        ->where('is_active', true)
        ->limit(6)
        ->get();
});

Route::get('/categories', function () {
    return Category::where('is_active', true)->get();
});

Route::get('/products/search', function (Request $request) {
    $query = MongoDBProduct::where('is_active', true);
    
    if ($request->filled('q')) {
        $search = $request->q;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%');
        });
    }
    
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }
    
    return $query->paginate(12);
});

// Protected API endpoints (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // User profile
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // User orders
    Route::get('/orders', function (Request $request) {
        return $request->user()->orders()->with('items')->get();
    });
    
    // API Token Management
    Route::post('/tokens', function (Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'abilities' => 'sometimes|array'
        ]);

        $token = $request->user()->createToken(
            $request->name,
            $request->abilities ?? ['*']
        );

        return response()->json([
            'token' => $token->plainTextToken,
            'name' => $request->name,
            'abilities' => $token->accessToken->abilities
        ]);
    });
    
    Route::get('/tokens', function (Request $request) {
        return $request->user()->tokens;
    });
    
    Route::delete('/tokens/{id}', function (Request $request, $id) {
        $request->user()->tokens()->where('id', $id)->delete();
        return response()->json(['message' => 'Token revoked successfully']);
    });
});

// Admin API endpoints (require admin privileges)
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/products', function () {
        return MongoDBProduct::paginate(15);
    });
    
    Route::post('/products', function (Request $request) {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id'
        ]);
        
        return MongoDBProduct::create($validated);
    });
    
    Route::get('/users', function () {
        return \App\Models\User::paginate(15);
    });
});
