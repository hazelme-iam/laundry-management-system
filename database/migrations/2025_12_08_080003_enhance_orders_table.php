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
            // Drop existing status column to recreate with enhanced values
            $table->dropColumn('status');
        });

        Schema::table('orders', function (Blueprint $table) {
            // Enhanced status with better workflow tracking
            $table->enum('status', [
                'pending',           // Initial order state
                'approved',          // Order approved by staff
                'rejected',          // Order rejected
                'picked_up',         // Laundry picked up from customer
                'washing',           // Currently being washed
                'drying',            // Currently being dried
                'folding',           // Being folded/prepared
                'quality_check',     // Quality inspection
                'ready',             // Ready for pickup/delivery
                'delivery_pending',  // Awaiting delivery
                'completed',         // Order completed
                'cancelled'          // Order cancelled
            ])->default('pending');

            // Add machine assignment fields
            $table->foreignId('primary_washer_id')->nullable()->constrained('machines');
            $table->foreignId('primary_dryer_id')->nullable()->constrained('machines');

            // Enhanced timing fields for better tracking
            $table->dateTime('picked_up_at')->nullable();
            $table->dateTime('quality_check_start')->nullable();
            $table->dateTime('quality_check_end')->nullable();
            $table->dateTime('delivery_started_at')->nullable();
            $table->dateTime('delivery_completed_at')->nullable();

            // Load management fields
            $table->integer('total_loads')->default(1); // Number of loads for this order
            $table->decimal('weight_per_load', 8, 2)->nullable(); // Average weight per load

            // Priority and service level
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('service_type', ['standard', 'express', 'premium'])->default('standard');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'primary_washer_id',
                'primary_dryer_id',
                'picked_up_at',
                'quality_check_start',
                'quality_check_end',
                'delivery_started_at',
                'delivery_completed_at',
                'total_loads',
                'weight_per_load',
                'priority',
                'service_type'
            ]);
        });

        Schema::table('orders', function (Blueprint $table) {
            // Recreate original status column
            $table->enum('status', ['pending', 'in_progress', 'ready', 'completed', 'cancelled']);
        });
    }
};
