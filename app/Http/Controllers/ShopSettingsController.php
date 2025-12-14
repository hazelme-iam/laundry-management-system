<?php

namespace App\Http\Controllers;

use App\Models\ShopSetting;
use App\Services\ShopService;
use Illuminate\Http\Request;

class ShopSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'admin') {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });
    }

    public function edit()
    {
        $settings = ShopSetting::get();
        return view('admin.settings.shop', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'shop_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'base_price_per_kg' => 'required|numeric|min:0',
            'detergent_price' => 'required|numeric|min:0',
            'fabric_conditioner_price' => 'required|numeric|min:0',
            'washing_time_minutes' => 'required|integer|min:1',
            'drying_time_per_kg' => 'required|integer|min:1',
            'min_drying_time' => 'required|integer|min:1',
            'max_drying_time' => 'required|integer|min:1',
            'business_hours' => 'nullable|string',
            'about' => 'nullable|string',
        ]);

        $settings = ShopSetting::get();
        
        $settings->update([
            'shop_name' => $data['shop_name'],
            'owner_name' => $data['owner_name'],
            'address' => $data['address'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'base_price_per_kg' => $data['base_price_per_kg'],
            'add_on_prices' => [
                'detergent' => $data['detergent_price'],
                'fabric_conditioner' => $data['fabric_conditioner_price'],
            ],
            'washing_time_minutes' => $data['washing_time_minutes'],
            'drying_time_per_kg' => $data['drying_time_per_kg'],
            'min_drying_time' => $data['min_drying_time'],
            'max_drying_time' => $data['max_drying_time'],
            'business_hours' => $data['business_hours'],
            'about' => $data['about'],
        ]);

        ShopService::refreshCache();

        return redirect()->route('admin.settings.shop.edit')
            ->with('success', 'Shop settings updated successfully!');
    }
}
