<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopSetting extends Model
{
    protected $table = 'shop_settings';

    protected $fillable = [
        'shop_name',
        'owner_name',
        'address',
        'phone',
        'email',
        'base_price_per_kg',
        'add_on_prices',
        'washing_time_minutes',
        'drying_time_per_kg',
        'min_drying_time',
        'max_drying_time',
        'business_hours',
        'about',
    ];

    protected $casts = [
        'add_on_prices' => 'array',
        'base_price_per_kg' => 'decimal:2',
    ];

    public static function get()
    {
        return self::first() ?? self::create([
            'shop_name' => 'Laundry Stream Wash N\' Dry',
            'owner_name' => 'Ms. Fe',
            'address' => 'Zone 1, E. Jacinto Street, Poblacion, Tagoloan, Misamis Oriental',
        ]);
    }

    public function getAddOnPrice($addOn)
    {
        return $this->add_on_prices[$addOn] ?? 0;
    }
}
