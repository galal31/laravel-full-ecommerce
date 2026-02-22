<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {

            $table->id();

            // Foreign Keys
            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();

            $table->foreignId('brand_id')
                ->constrained('brands')
                ->cascadeOnDelete();

            // Basic Info
            $table->string('name');
            $table->text('small_desc')->nullable();
            $table->longText('desc')->nullable();

            $table->boolean('status')->default(true);

            $table->string('sku');

            $table->date('available_for')->nullable();

            $table->unsignedBigInteger('views')->default(0);

            // Pricing
            $table->decimal('price', 10, 2);
            $table->decimal('discount', 10, 2)->nullable();

            $table->date('start_discount')->nullable();
            $table->date('end_discount')->nullable();

            // Stock
            $table->boolean('manage_stock')->default(false);
            $table->integer('quantity')->nullable();
            $table->boolean('available_in_stock')->default(true);

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
