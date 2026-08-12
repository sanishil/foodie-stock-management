<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Delivery; // 👈 Order ki jagah Delivery model import
use App\Models\Member;
use App\Models\User;
use Exception;

class DashboardController extends Controller
{
    // 👑 Super Admin Dashboard Analytics API (Using Delivery Model)
    public function getAdminAnalytics()
    {
        try {
            // 1. All-Time Revenue & Today's Revenue
            $totalRevenue = Delivery::sum('total') ?? 0;
            $todaysRevenue = Delivery::whereDate('created_at', \Carbon\Carbon::today())->sum('total') ?? 0;

            // 2. Total Orders & Today's Orders
            $totalOrders = Delivery::count();
            $todaysOrders = Delivery::whereDate('created_at', \Carbon\Carbon::today())->count();

            // 3. Active Customers Count
            $activeCustomers = User::where('role', 'Customer')->count();
            if ($activeCustomers === 0) {
                $activeCustomers = Member::where('status', 'Active')->count();
            }

            // 4. Total Staff/Employees Count
            $totalStaff = User::where('role', '!=', 'customer')->count();

            // 5. 🎯 Recent Orders (Sirf Aaj ke orders table ke liye)
            $recentOrders = Delivery::whereDate('created_at', \Carbon\Carbon::today())
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'id'       => $item->order_number ?? ('ORD-' . $item->id),
                        'customer' => $item->customer_name ?? 'Customer',
                        'amount'   => (float) ($item->total ?? 0),
                        'status'   => ucfirst($item->status ?? 'Pending'),
                        'date'     => $item->created_at ? $item->created_at->format('Y-m-d') : date('Y-m-d')
                    ];
                });

            return response()->json([
                'totalRevenue'    => (float) $totalRevenue,
                'todaysRevenue'   => (float) $todaysRevenue,
                'totalOrders'     => (int) $totalOrders,
                'todaysOrders'    => (int) $todaysOrders,
                'activeCustomers' => (int) $activeCustomers,
                'totalStaff'      => (int) $totalStaff,
                'recentOrders'    => $recentOrders
            ], 200);

        } catch (Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function getMetrics()
    {
        try {
            return response()->json([
                'total_inventory'   => Inventory::count(),
                'low_stock_items'   => Inventory::where('quantity', '<=', 10)->count(),
                'active_deliverys' => Delivery::where('status', 'Out for Delivery')->count(),
                'total_members'     => Member::where('status', 'Active')->count(),
            ], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function getActivities()
    {
        try {
            $activities = [];

            $lowStockItems = Inventory::where('quantity', '<=', 10)->latest()->take(3)->get();
            foreach ($lowStockItems as $item) {
                $activities[] = [
                    'id'   => 'low_stock_' . $item->id,
                    'text' => "Low stock warning for {$item->item_name} ({$item->quantity} {$item->unit} remaining)",
                    'time' => $item->updated_at ? $item->updated_at->diffForHumans() : 'Recently'
                ];
            }

            $recentdeliverys = Delivery::latest()->take(3)->get();
            foreach ($recentdeliverys as $del) {
                $amt = $del->amount ?? $del->total_amount ?? 0;
                $activities[] = [
                    'id'   => 'delivery_' . $del->id,
                    'text' => "Order/Delivery #{$del->id} status: {$del->status} (₹{$amt})",
                    'time' => $del->created_at ? $del->created_at->diffForHumans() : 'Recently'
                ];
            }

            return response()->json($activities, 200);

        } catch (Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}