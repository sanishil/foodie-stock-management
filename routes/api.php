<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\DeliveryController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\KitchenStocks;

Route::post('login', [LoginController::class, 'login']);

// 🔴 PHOTO UPLOAD (Changed to POST to match Angular)
Route::post('uploadcustomerimage/{id}', [CustomerController::class, 'uploadcustomerimage']);

// 🔴 CHANGE PASSWORD ROUTE
Route::post('customers/{id}/change-password', [CustomerController::class, 'changePassword']);

Route::apiResource('delivery', DeliveryController::class);
Route::get('totalorders', [DeliveryController::class, 'totalOrder']);
Route::get('orderhistory/{customer_id}', [DeliveryController::class, 'getDelivery']);
Route::apiResource('kitchenstocks', KitchenStocks::class);

// 📦 Resource Routes (Auto-maps CRUD: GET, POST, PUT, DELETE)
Route::apiResource('employees', EmployeeController::class);
Route::apiResource('customers', CustomerController::class); // Yeh automatically show, update, destroy map karega
Route::apiResource('inventory', InventoryController::class);

// 👑 Membership Endpoints
Route::get('memberships/plans', [MembershipController::class, 'getPlans']);
Route::get('memberships/members', [MembershipController::class, 'getMembers']);
Route::post('memberships/members', [MembershipController::class, 'addMember']);
Route::put('memberships/members/{id}/status', [MembershipController::class, 'updateStatus']);
Route::delete('memberships/members/{id}', [MembershipController::class, 'cancelMembership']);

// ⚙️ Settings Endpoints
Route::get('settings', [SettingsController::class, 'getSettings']);
Route::put('settings', [SettingsController::class, 'updateSettings']);



// 📊 Dashboard Endpoints
Route::get('dashboard/metrics', [DashboardController::class, 'getMetrics']);
Route::get('dashboard/activities', [DashboardController::class, 'getActivities']);
Route::get('dashboard/analytics', [DashboardController::class, 'getAdminAnalytics']); // 👈 Yeh naya route add karo

// 🛒 Custom Inventory Action
Route::put('inventory/{id}/reduce-stock', [InventoryController::class, 'reduceStock']);