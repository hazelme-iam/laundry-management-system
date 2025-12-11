<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loads', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            
            // Remove old fields that are no longer needed
            $columnsToRemove = [
                'capacity_utilization',
                'is_consolidated',
                'created_by',
                'updated_by',
                'wash_start',
                'wash_end',
                'dry_start',
                'dry_end',
                'folding_start',
                'folding_end',
                'notes'
            ];
            
            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('loads', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('loads', function (Blueprint $table) {
            // Add back the old fields if needed
            $table->decimal('capacity_utilization', 5, 2)->nullable();
            $table->boolean('is_consolidated')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('wash_start')->nullable();
            $table->timestamp('wash_end')->nullable();
            $table->timestamp('dry_start')->nullable();
            $table->timestamp('dry_end')->nullable();
            $table->timestamp('folding_start')->nullable();
            $table->timestamp('folding_end')->nullable();
            $table->text('notes')->nullable();
        });
    }
};
