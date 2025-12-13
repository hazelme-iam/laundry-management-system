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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('confirmed_weight', 8, 2)->nullable()->after('weight');
            $table->dateTime('weight_confirmed_at')->nullable()->after('confirmed_weight');
            $table->foreignId('weight_confirmed_by')->nullable()->constrained('users')->after('weight_confirmed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeignIdFor('users', 'weight_confirmed_by');
            $table->dropColumn(['confirmed_weight', 'weight_confirmed_at', 'weight_confirmed_by']);
        });
    }
};
