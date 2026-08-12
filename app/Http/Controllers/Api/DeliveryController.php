<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Exception;
use Carbon\Carbon;
class DeliveryController extends Controller
{
    public function index()
    {
        try {
            $orders = Delivery::whereDate('created_at', Carbon::today())
                ->orderBy('id', 'asc')
                ->get();

            return response()->json($orders, 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function totalOrder()
    {
        try {
            return response()->json(Delivery::count());
        } catch (Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function getDelivery(string $customer_id)
    {
        try {
            $delivery = Delivery::where('customer_id', $customer_id)
                ->distinct()
                ->get();

            if ($delivery->isEmpty()) {
                return response()->json([
                    'message' => 'Order not found'
                ], 404);
            }

            return response()->json($delivery, 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'required|string',
                'customer_name' => 'required|string',
                'delivery_address' => 'required|string',
                'driver_name' => 'nullable|string',
                'driver_phone' => 'nullable|string',
                'items' => 'nullable|string',
                'total' => 'required|numeric',
                'status' => 'nullable|string'
            ]);

            $validated['status'] = $validated['status'] ?? 'Preparing';
            $validated['driver_name'] = $validated['driver_name'] ?? 'Unassigned';

            // Generate Order ID
            $lastOrderID = Delivery::latest('id')->first();

            if ($lastOrderID) {
                $number = (int) str_replace('ORD', '', $lastOrderID->order_number);
                $validated['order_number'] = 'ORD' . ($number + 1);
            } else {
                $validated['order_number'] = 'ORD1';
            }
            $delivery = Delivery::create($validated);
            return response()->json($delivery, 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $delivery = Delivery::find($id);
            if (!$delivery)
                return response()->json(['message' => 'Delivery not found'], 404);

            $delivery->update($request->all());
            return response()->json($delivery, 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $delivery = Delivery::find($id);
            if (!$delivery)
                return response()->json(['message' => 'Delivery not found'], 404);

            $delivery->delete();
            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}