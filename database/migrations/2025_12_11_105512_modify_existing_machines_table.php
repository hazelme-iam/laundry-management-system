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
        Schema::table('machines', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('machines', 'current_order_id')) {
                $table->unsignedBigInteger('current_order_id')->nullable();
                $table->foreign('current_order_id')->references('id')->on('orders')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('machines', 'washing_start')) {
                $table->timestamp('washing_start')->nullable();
            }
            
            if (!Schema::hasColumn('machines', 'washing_end')) {
                $table->timestamp('washing_end')->nullable();
            }
            
            if (!Schema::hasColumn('machines', 'drying_start')) {
                $table->timestamp('drying_start')->nullable();
            }
            
            if (!Schema::hasColumn('machines', 'drying_end')) {
                $table->timestamp('drying_end')->nullable();
            }

            // Modify status enum if needed
            $table->enum('status', ['idle', 'in_use', 'maintenance'])->default('idle')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->dropForeign(['current_order_id']);
            $table->dropColumn([
                'current_order_id',
                'washing_start',
                'washing_end',
                'drying_start',
                'drying_end'
            ]);
        });
    }
};
