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
        // If the table doesn't exist yet, skip this migration.
        if (!Schema::hasTable('mongo_products')) {
            return;
        }

        Schema::table('mongo_products', function (Blueprint $table) {
            // Make fields nullable for MongoDB-style flexibility
            if (Schema::hasColumn('mongo_products', 'short_description')) {
                $table->text('short_description')->nullable()->change();
            }
            if (Schema::hasColumn('mongo_products', 'video_url')) {
                $table->text('video_url')->nullable()->change();
            }
            if (Schema::hasColumn('mongo_products', 'introduction_video')) {
                $table->text('introduction_video')->nullable()->change();
            }
            if (Schema::hasColumn('mongo_products', 'meta_title')) {
                $table->text('meta_title')->nullable()->change();
            }
            if (Schema::hasColumn('mongo_products', 'meta_description')) {
                $table->text('meta_description')->nullable()->change();
            }
            if (Schema::hasColumn('mongo_products', 'weight')) {
                $table->decimal('weight', 8, 2)->nullable()->change();
            }
            if (Schema::hasColumn('mongo_products', 'rating_average')) {
                $table->decimal('rating_average', 3, 1)->default(0)->change();
            }
            if (Schema::hasColumn('mongo_products', 'rating_count')) {
                $table->integer('rating_count')->default(0)->change();
            }
            if (Schema::hasColumn('mongo_products', 'views_count')) {
                $table->integer('views_count')->default(0)->change();
            }
            if (Schema::hasColumn('mongo_products', 'sales_count')) {
                $table->integer('sales_count')->default(0)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('mongo_products')) {
            return;
        }

        Schema::table('mongo_products', function (Blueprint $table) {
            // Revert nullable changes if needed
            if (Schema::hasColumn('mongo_products', 'short_description')) {
                $table->text('short_description')->nullable(false)->change();
            }
            if (Schema::hasColumn('mongo_products', 'video_url')) {
                $table->text('video_url')->nullable(false)->change();
            }
            if (Schema::hasColumn('mongo_products', 'introduction_video')) {
                $table->text('introduction_video')->nullable(false)->change();
            }
            if (Schema::hasColumn('mongo_products', 'meta_title')) {
                $table->text('meta_title')->nullable(false)->change();
            }
            if (Schema::hasColumn('mongo_products', 'meta_description')) {
                $table->text('meta_description')->nullable(false)->change();
            }
            if (Schema::hasColumn('mongo_products', 'weight')) {
                $table->decimal('weight', 8, 2)->nullable(false)->change();
            }
        });
    }
};