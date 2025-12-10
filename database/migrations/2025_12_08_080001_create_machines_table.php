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
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Washer 01", "Dryer 02"
            $table->enum('type', ['washer', 'dryer']);
            $table->decimal('capacity_kg', 8, 2); // e.g., 8.00 for 8kg capacity
            $table->enum('status', ['available', 'in_use', 'maintenance', 'out_of_order'])->default('available');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
