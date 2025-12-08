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
        Schema::table('loads', function (Blueprint $table) {
            $table->decimal('capacity_utilization', 5, 2)->nullable(); // Percentage like 75.50
            $table->boolean('is_consolidated')->default(false); // Combines multiple orders
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loads', function (Blueprint $table) {
            $table->dropColumn(['capacity_utilization', 'is_consolidated']);
        });
    }
};
