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
        Schema::create('deliverys', function (Blueprint $table) {
            $table->id();
            $table->string('order_number');
            $table->string('customer_name');
            $table->string('delivery_address');
            $table->string('driver_name')->nullable()->default('Unassigned');
            $table->string('driver_phone')->nullable();
            $table->text('items')->nullable();
            $table->decimal('total', 10, 2)->default(0.00);
            $table->string('status')->default('Preparing');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliverys');
    }
};
