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
            $table->string('_id')->unique()->nullable()->after('id');
            $table->json('document_data')->nullable()->after('sales_count');
            $table->index('_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mongo_products', function (Blueprint $table) {
            $table->dropIndex(['_id']);
            $table->dropColumn(['_id', 'document_data']);
        });
    }
};