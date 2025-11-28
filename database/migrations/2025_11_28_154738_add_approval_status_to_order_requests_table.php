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
        Schema::table('order_requests', function (Blueprint $table) {
            // Drop the existing enum column
            $table->dropColumn('status');
        });
        
        Schema::table('order_requests', function (Blueprint $table) {
            // Recreate the enum column with the new values
            $table->enum('status', ['pending', 'approved', 'rejected', 'in_progress', 'ready', 'completed', 'cancelled'])->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_requests', function (Blueprint $table) {
            // Drop the updated enum column
            $table->dropColumn('status');
        });
        
        Schema::table('order_requests', function (Blueprint $table) {
            // Recreate the original enum column
            $table->enum('status', ['pending', 'in_progress', 'ready', 'completed', 'cancelled'])->default('pending');
        });
    }
};
