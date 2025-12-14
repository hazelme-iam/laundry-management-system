<?php

namespace App\Services;

use App\Models\ShopSetting;

class ShopService
{
    private static $settings = null;

    public static function settings()
    {
        if (self::$settings === null) {
            self::$settings = ShopSetting::get();
        }
        return self::$settings;
    }

    public static function getShopName()
    {
        return self::settings()->shop_name;
    }

    public static function getOwnerName()
    {
        return self::settings()->owner_name;
    }

    public static function getAddress()
    {
        return self::settings()->address;
    }

    public static function getPhone()
    {
        return self::settings()->phone;
    }

    public static function getEmail()
    {
        return self::settings()->email;
    }

    public static function getBasePricePerKg()
    {
        return self::settings()->base_price_per_kg;
    }

    public static function getAddOnPrices()
    {
        return self::settings()->add_on_prices ?? [];
    }

    public static function getAddOnPrice($addOn)
    {
        return self::settings()->getAddOnPrice($addOn);
    }

    public static function getWashingTimeMinutes()
    {
        return self::settings()->washing_time_minutes;
    }

    public static function getDryingTimePerKg()
    {
        return self::settings()->drying_time_per_kg;
    }

    public static function getMinDryingTime()
    {
        return self::settings()->min_drying_time;
    }

    public static function getMaxDryingTime()
    {
        return self::settings()->max_drying_time;
    }

    public static function calculateOrderTotal($weight, $addOns = [])
    {
        $basePrice = self::getBasePricePerKg();
        $subtotal = $basePrice * max(1, $weight);

        $addOnPrices = self::getAddOnPrices();
        
        foreach ($addOns as $addOn) {
            if (isset($addOnPrices[$addOn])) {
                $subtotal += $addOnPrices[$addOn];
            }
        }

        return $subtotal;
    }

    public static function calculateDryingTime($weight)
    {
        $baseTime = $weight * self::getDryingTimePerKg();
        $minTime = self::getMinDryingTime();
        $maxTime = self::getMaxDryingTime();

        if ($baseTime < $minTime) {
            return $minTime;
        } elseif ($baseTime > $maxTime) {
            return $maxTime;
        }

        return (int) $baseTime;
    }

    public static function refreshCache()
    {
        self::$settings = null;
    }
}
