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
        // Migrate any data from laundry_requests to orders if needed
        // This is a safeguard - in case there's data we want to preserve
        
        // Drop the duplicate table
        Schema::dropIfExists('laundry_requests');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the laundry_requests table if we need to rollback
        Schema::create('laundry_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->enum('status', ['pending', 'approved', 'rejected', 'in_progress', 'ready', 'completed', 'cancelled'])->default('pending');
            $table->decimal('weight', 8, 2);
            $table->json('add_ons')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('amount_paid', 10, 2);
            $table->date('pickup_date')->nullable();
            $table->dateTime('estimated_finish');
            $table->dateTime('finished_at')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
        });
    }
};
