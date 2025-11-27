<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_requests', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('customer_id')->constrained('customers'); // Foreign key to customers
            $table->enum('status', ['pending', 'in_progress', 'ready', 'completed', 'cancelled']);
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

            // Optional: connect to orders table if an order request can become an order
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_requests');
    }
};
