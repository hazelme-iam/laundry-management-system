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
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('customer_id')->constrained('customers'); // Foreign key to customers
            $table->enum('status', ['pending', 'in_progress', 'ready', 'completed', 'cancelled']);
            $table->decimal('weight', 8, 2); // KG value, adjust precision if needed
            $table->json('add_ons')->nullable(); // e.g. stain_removal, fragrance
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('amount_paid', 10, 2);
            $table->date('pickup_date')->nullable();
            $table->dateTime('estimated_finish');
            $table->dateTime('finished_at')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->constrained('users'); // Assuming staff are in users table
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
