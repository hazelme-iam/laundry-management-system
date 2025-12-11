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
            $table->unsignedBigInteger('assigned_washer_id')->nullable();
            $table->unsignedBigInteger('assigned_dryer_id')->nullable();
            $table->timestamp('washing_start')->nullable();
            $table->timestamp('washing_end')->nullable();
            $table->timestamp('drying_start')->nullable();
            $table->timestamp('drying_end')->nullable();
            
            $table->foreign('assigned_washer_id')->references('id')->on('machines')->onDelete('set null');
            $table->foreign('assigned_dryer_id')->references('id')->on('machines')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['assigned_washer_id']);
            $table->dropForeign(['assigned_dryer_id']);
            $table->dropColumn([
                'assigned_washer_id',
                'assigned_dryer_id',
                'washing_start',
                'washing_end',
                'drying_start',
                'drying_end'
            ]);
        });
    }
};
