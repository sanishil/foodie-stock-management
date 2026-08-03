<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('eid');
            $table->string('name');
            $table->string('role');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('avatar_url')->nullable();
            $table->enum('status', ['Active', 'On Leave', 'Resigned', 'Suspended'])->default('Active');
            $table->timestamps(); // Created_at & Updated_at auto-manage hoge
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};