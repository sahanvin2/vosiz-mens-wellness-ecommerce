<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mongo_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('short_description');
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->integer('discount_percentage')->default(0);
            $table->string('sku')->unique();
            $table->unsignedBigInteger('category_id');
            $table->string('category_name');
            $table->json('images')->nullable();
            $table->string('video_url')->nullable();
            $table->string('introduction_video')->nullable();
            $table->json('features')->nullable();
            $table->json('specifications')->nullable();
            $table->json('ingredients')->nullable();
            $table->text('usage_instructions')->nullable();
            $table->json('tags')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('stock_quantity')->default(0);
            $table->decimal('weight', 8, 2)->nullable();
            $table->json('dimensions')->nullable();
            $table->decimal('rating_average', 3, 1)->default(0);
            $table->integer('rating_count')->default(0);
            $table->integer('views_count')->default(0);
            $table->integer('sales_count')->default(0);
            $table->timestamps();
            
            $table->index(['category_id', 'is_active']);
            $table->index('is_featured');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mongo_products');
    }
};
