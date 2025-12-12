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
            // Indexes for frequently queried columns
            $table->index(['status'], 'orders_status_index');
            $table->index(['customer_id'], 'orders_customer_id_index');
            $table->index(['created_by'], 'orders_created_by_index');
            $table->index(['pickup_date'], 'orders_pickup_date_index');
            $table->index(['estimated_finish'], 'orders_estimated_finish_index');
            $table->index(['created_at'], 'orders_created_at_index');
            
            // Composite indexes for common query patterns
            $table->index(['status', 'created_at'], 'orders_status_created_at_index');
            $table->index(['customer_id', 'status'], 'orders_customer_status_index');
        });

        Schema::table('customers', function (Blueprint $table) {
            // Indexes for customer queries
            $table->index(['user_id'], 'customers_user_id_index');
            $table->index(['customer_type'], 'customers_type_index');
            $table->index(['phone'], 'customers_phone_index');
            $table->index(['last_order_at'], 'customers_last_order_index');
            $table->index(['created_at'], 'customers_created_at_index');
        });

        Schema::table('machines', function (Blueprint $table) {
            // Indexes for machine queries
            $table->index(['type'], 'machines_type_index');
            $table->index(['status'], 'machines_status_index');
            $table->index(['type', 'status'], 'machines_type_status_index');
        });

        Schema::table('loads', function (Blueprint $table) {
            // Indexes for load queries
            $table->index(['order_id'], 'loads_order_id_index');
            $table->index(['washer_machine_id'], 'loads_washer_index');
            $table->index(['dryer_machine_id'], 'loads_dryer_index');
            $table->index(['status'], 'loads_status_index');
            $table->index(['order_id', 'status'], 'loads_order_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_status_index');
            $table->dropIndex('orders_customer_id_index');
            $table->dropIndex('orders_created_by_index');
            $table->dropIndex('orders_pickup_date_index');
            $table->dropIndex('orders_estimated_finish_index');
            $table->dropIndex('orders_created_at_index');
            $table->dropIndex('orders_status_created_at_index');
            $table->dropIndex('orders_customer_status_index');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('customers_user_id_index');
            $table->dropIndex('customers_type_index');
            $table->dropIndex('customers_phone_index');
            $table->dropIndex('customers_last_order_index');
            $table->dropIndex('customers_created_at_index');
        });

        Schema::table('machines', function (Blueprint $table) {
            $table->dropIndex('machines_type_index');
            $table->dropIndex('machines_status_index');
            $table->dropIndex('machines_type_status_index');
        });

        Schema::table('loads', function (Blueprint $table) {
            $table->dropIndex('loads_order_id_index');
            $table->dropIndex('loads_washer_index');
            $table->dropIndex('loads_dryer_index');
            $table->dropIndex('loads_status_index');
            $table->dropIndex('loads_order_status_index');
        });
    }
};
