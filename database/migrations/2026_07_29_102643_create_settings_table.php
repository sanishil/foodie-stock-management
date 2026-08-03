<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('restaurant_name')->default('Foodie Restro');
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('address')->nullable();
            $table->string('opening_time')->default('09:00 AM');
            $table->string('closing_time')->default('11:00 PM');
            $table->string('currency')->default('INR');
            $table->decimal('tax_percentage', 5, 2)->default(5.00);
            $table->decimal('delivery_charge', 8, 2)->default(40.00);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('settings');
    }
};