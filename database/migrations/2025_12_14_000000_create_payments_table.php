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
        // Check if payments table already exists
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->decimal('amount', 10, 2);
                $table->dateTime('payment_date');
                $table->foreignId('recorded_by')->constrained('users');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index('order_id');
                $table->index('payment_date');
            });
        } else {
            // If table exists, add missing columns if they don't exist
            Schema::table('payments', function (Blueprint $table) {
                if (!Schema::hasColumn('payments', 'payment_date')) {
                    $table->dateTime('payment_date')->nullable();
                }
                if (!Schema::hasColumn('payments', 'recorded_by')) {
                    $table->foreignId('recorded_by')->nullable()->constrained('users');
                }
                if (!Schema::hasColumn('payments', 'notes')) {
                    $table->text('notes')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
