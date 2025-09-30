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
        Schema::table('mongo_products', function (Blueprint $table) {
            // Make fields nullable for MongoDB-style flexibility
            $table->text('short_description')->nullable()->change();
            $table->text('video_url')->nullable()->change();
            $table->text('introduction_video')->nullable()->change();
            $table->text('meta_title')->nullable()->change();
            $table->text('meta_description')->nullable()->change();
            $table->decimal('weight', 8, 2)->nullable()->change();
            $table->decimal('rating_average', 3, 1)->default(0)->change();
            $table->integer('rating_count')->default(0)->change();
            $table->integer('views_count')->default(0)->change();
            $table->integer('sales_count')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mongo_products', function (Blueprint $table) {
            // Revert nullable changes if needed
            $table->text('short_description')->nullable(false)->change();
            $table->text('video_url')->nullable(false)->change();
            $table->text('introduction_video')->nullable(false)->change();
            $table->text('meta_title')->nullable(false)->change();
            $table->text('meta_description')->nullable(false)->change();
            $table->decimal('weight', 8, 2)->nullable(false)->change();
        });
    }
};