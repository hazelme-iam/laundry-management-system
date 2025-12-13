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
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'cash_given')) {
                $table->decimal('cash_given', 10, 2)->nullable()->after('amount');
            }
            if (!Schema::hasColumn('payments', 'change')) {
                $table->decimal('change', 10, 2)->nullable()->after('cash_given');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['cash_given', 'change']);
        });
    }
};
