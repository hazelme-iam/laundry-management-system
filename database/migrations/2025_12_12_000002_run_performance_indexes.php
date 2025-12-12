<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This is a helper migration to run the index creation
        // The actual indexes are defined in 2025_12_12_000001_add_performance_indexes.php
        // This ensures the indexes are created in the correct order
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No action needed as this is just a helper migration
    }
};
