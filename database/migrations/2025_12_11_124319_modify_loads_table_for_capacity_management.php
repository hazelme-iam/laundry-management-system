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
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('loads', 'order_id')) {
                $table->foreignId('order_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('loads', 'weight')) {
                $table->decimal('weight', 8, 2);
            }
            if (!Schema::hasColumn('loads', 'status')) {
                $table->enum('status', ['pending', 'washing', 'drying', 'completed'])->default('pending');
            }
            if (!Schema::hasColumn('loads', 'washer_machine_id')) {
                $table->foreignId('washer_machine_id')->nullable()->constrained('machines')->onDelete('set null');
            }
            if (!Schema::hasColumn('loads', 'dryer_machine_id')) {
                $table->foreignId('dryer_machine_id')->nullable()->constrained('machines')->onDelete('set null');
            }
            if (!Schema::hasColumn('loads', 'washing_start')) {
                $table->timestamp('washing_start')->nullable();
            }
            if (!Schema::hasColumn('loads', 'washing_end')) {
                $table->timestamp('washing_end')->nullable();
            }
            if (!Schema::hasColumn('loads', 'drying_start')) {
                $table->timestamp('drying_start')->nullable();
            }
            if (!Schema::hasColumn('loads', 'drying_end')) {
                $table->timestamp('drying_end')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loads', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['washer_machine_id']);
            $table->dropForeign(['dryer_machine_id']);
            $table->dropColumn([
                'order_id',
                'weight', 
                'status',
                'washer_machine_id',
                'dryer_machine_id',
                'washing_start',
                'washing_end',
                'drying_start',
                'drying_end'
            ]);
        });
    }
};
