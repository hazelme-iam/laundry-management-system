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
        Schema::create('loads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->foreignId('washer_machine_id')->nullable()->constrained('machines');
            $table->foreignId('dryer_machine_id')->nullable()->constrained('machines');
            $table->decimal('weight', 8, 2); // Actual weight for this load
            $table->enum('status', ['pending', 'washing', 'drying', 'folding', 'completed'])->default('pending');
            
            // Timing fields for each process
            $table->dateTime('wash_start')->nullable();
            $table->dateTime('wash_end')->nullable();
            $table->dateTime('dry_start')->nullable();
            $table->dateTime('dry_end')->nullable();
            $table->dateTime('folding_start')->nullable();
            $table->dateTime('folding_end')->nullable();
            
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loads');
    }
};
