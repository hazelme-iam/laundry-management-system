<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('customer_type');
        });
        
        Schema::table('customers', function (Blueprint $table) {
            $table->enum('customer_type', ['walk-in', 'regular', 'vip', 'online'])->default('walk-in');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('customer_type');
        });
        
        Schema::table('customers', function (Blueprint $table) {
            $table->enum('customer_type', ['walk-in', 'regular', 'vip'])->default('walk-in');
        });
    }
};
