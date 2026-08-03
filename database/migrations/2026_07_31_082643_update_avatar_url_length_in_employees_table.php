<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // avatar_url ko string (255) se badha kar text (65,535 chars) kar rahe hain
            $table->text('avatar_url')->nullable()->change();
            $table->string('phone', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('avatar_url', 255)->nullable()->change();
            $table->string('phone', 255)->change();
        });
    }
};