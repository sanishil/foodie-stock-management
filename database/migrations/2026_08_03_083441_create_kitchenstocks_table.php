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
        Schema::create('kitchenstocks', function (Blueprint $table) {
            $table->id();
            $table->string("eid");
            $table->string('ingredient_name');
            $table->integer('quantity');
            $table->string('unit');
            $table->integer('minimum_stock_alert');
            $table->integer('request_item');
            $table->integer('request_to_admin');
            $table->string('status');
            $table->string('user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kitchenstocks');
    }
};
