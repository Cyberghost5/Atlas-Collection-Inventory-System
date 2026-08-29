<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name'); // e.g. "Atlas Heavyweight Hoodie"
            $table->string('sku')->unique(); // e.g. "AUC-HD-BLK-M"
            $table->string('size')->default('M'); // XS, S, M, L, XL, XXL, One Size
            $table->string('color')->nullable(); // Washed Black, Bone White, Olive
            $table->string('image')->nullable(); // Product picture URL/filename
            $table->string('barcode')->nullable();
            $table->text('description')->nullable();
            
            $table->enum('usage_type', ['retail', 'display_sample', 'both'])->default('retail');
            
            $table->string('unit')->default('piece'); // piece, set, pair, pack
            $table->decimal('cost_price', 10, 2);
            $table->decimal('selling_price', 10, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_level')->default(5);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
