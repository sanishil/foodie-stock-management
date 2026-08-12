<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. CUSTOMERS TABLE UPDATE (Bina data loss ke)
        Schema::table('customers', function (Blueprint $table) {
            // Naye columns add kar rahe hain
            if (!Schema::hasColumn('customers', 'address')) {
                $table->string('address')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('customers', 'membership')) {
                $table->string('membership')->default('Standard')->after('address');
            }
            
            // Existing 'photo' ko nullable bana rahe hain taaki bina photo ke bhi account ban sake
            $table->string('photo')->nullable()->change();
        });

        // 2. USERS TABLE UPDATE (Bina data loss ke)
        Schema::table('users', function (Blueprint $table) {
            // eid aur customer_id ko nullable bana rahe hain taaki admin ya staff bina inke add ho sake
            $table->string('eid')->nullable()->change();
            $table->string('customer_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['address', 'membership']);
        });
    }
};