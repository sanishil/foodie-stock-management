<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Plans Table
        Schema::create('membership_plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_name');
            $table->decimal('price', 8, 2);
            $table->integer('duration_months');
            $table->integer('discount_percentage');
            $table->json('benefits')->nullable();
            $table->timestamps();
        });

        // Members Table
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->foreignId('plan_id')->constrained('membership_plans')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['Active', 'Expired', 'Cancelled'])->default('Active');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('members');
        Schema::dropIfExists('membership_plans');
    }
};