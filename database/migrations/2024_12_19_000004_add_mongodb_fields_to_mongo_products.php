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
        // If the table doesn't exist yet (fresh install), skip this migration.
        // The later create migration will define these fields when creating the table.
        if (!Schema::hasTable('mongo_products')) {
            return;
        }

        Schema::table('mongo_products', function (Blueprint $table) {
            if (!Schema::hasColumn('mongo_products', '_id')) {
                $table->string('_id')->unique()->nullable()->after('id');
            }
            if (!Schema::hasColumn('mongo_products', 'document_data')) {
                $table->json('document_data')->nullable()->after('sales_count');
            }
            // add index if not exists (Laravel doesn't provide index existence check easily)
            $table->index('_id');
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
            if (Schema::hasColumn('mongo_products', '_id')) {
                $table->dropIndex(['_id']);
                $table->dropColumn('_id');
            }
            if (Schema::hasColumn('mongo_products', 'document_data')) {
                $table->dropColumn('document_data');
            }
        });
    }
};