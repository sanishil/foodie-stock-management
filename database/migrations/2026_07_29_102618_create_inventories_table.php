<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->string('category');
            $table->decimal('price_per_unit', 8, 2);
            $table->integer('quantity');
            $table->string('unit')->default('pcs');
            $table->text('image_url')->nullable(); // 🌟 NEW IMAGE FIELD
            $table->integer('min_stock_level')->default(10);
            $table->string('status')->default('In Stock');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('inventories');
    }
};