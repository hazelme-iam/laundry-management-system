<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_settings', function (Blueprint $table) {
            $table->id();
            $table->string('shop_name')->default('Laundry Stream Wash N\' Dry');
            $table->string('owner_name')->default('Ms. Fe');
            $table->string('address')->default('Zone 1, E. Jacinto Street, Poblacion, Tagoloan, Misamis Oriental');
            $table->string('phone')->default('');
            $table->string('email')->default('');
            $table->decimal('base_price_per_kg', 10, 2)->default(150.00);
            $table->json('add_on_prices')->default('{"detergent": 16, "fabric_conditioner": 14}');
            $table->integer('washing_time_minutes')->default(38);
            $table->integer('drying_time_per_kg')->default(5);
            $table->integer('min_drying_time')->default(30);
            $table->integer('max_drying_time')->default(50);
            $table->text('business_hours')->nullable();
            $table->text('about')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('shop_settings')->insert([
            'shop_name' => 'Laundry Stream Wash N\' Dry',
            'owner_name' => 'Ms. Fe',
            'address' => 'Zone 1, E. Jacinto Street, Poblacion, Tagoloan, Misamis Oriental',
            'phone' => '',
            'email' => '',
            'base_price_per_kg' => 150.00,
            'add_on_prices' => json_encode(['detergent' => 16, 'fabric_conditioner' => 14]),
            'washing_time_minutes' => 38,
            'drying_time_per_kg' => 5,
            'min_drying_time' => 30,
            'max_drying_time' => 50,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_settings');
    }
};
