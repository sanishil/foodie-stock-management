<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function getSettings()
    {
        $settings = Setting::first();
        if (!$settings) {
            $settings = Setting::create([
                'restaurant_name' => 'Foodie Restro',
                'contact_email'   => 'admin@foodie.com',
                'contact_phone'   => '+91 9876543210',
                'address'         => 'Agartala, Tripura',
                'opening_time'    => '09:00 AM',
                'closing_time'    => '11:00 PM',
                'currency'        => 'INR',
                'tax_percentage'  => 5.00,
                'delivery_charge' => 40.00
            ]);
        }
        return response()->json($settings, 200);
    }

    public function updateSettings(Request $request)
    {
        $settings = Setting::first();
        if ($settings) {
            $settings->update($request->all());
            return response()->json($settings, 200);
        }
        return response()->json(['message' => 'Settings not found'], 404);
    }
}