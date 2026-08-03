<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Exception;

class InventoryController extends Controller
{
    // 1. GET ALL ITEMS
    public function index()
    {
        try {
            $items = Inventory::orderBy('id', 'desc')->get();
            return response()->json($items, 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // 2. CREATE NEW ITEM
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'item_name'       => 'required|string|max:255',
                'category'        => 'required|string|max:255',
                'quantity'        => 'required|integer',
                'unit'            => 'required|string|max:50',
                'image_url'       => 'nullable|string', // 🌟 NEW: Added image_url validation
                'min_stock_level' => 'nullable|integer',
                'price_per_unit'  => 'required|numeric',
                'status'          => 'nullable|string'
            ]);

            // Set default values if not provided
            $validated['min_stock_level'] = $validated['min_stock_level'] ?? 10;
            $validated['status'] = $validated['status'] ?? ($validated['quantity'] <= $validated['min_stock_level'] ? 'Low Stock' : 'In Stock');

            $item = Inventory::create($validated);
            return response()->json($item, 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // 3. UPDATE ITEM (General Update)
    public function update(Request $request, int $id)
    {
        try {
            $item = Inventory::find($id);
            if (!$item) return response()->json(['message' => 'Item not found'], 404);

            $item->update($request->all());

            // Auto-update status based on quantity after update
            if ($item->quantity <= 0) {
                $item->status = 'Out of Stock';
            } elseif ($item->quantity <= ($item->min_stock_level ?? 10)) {
                $item->status = 'Low Stock';
            } else {
                $item->status = 'In Stock';
            }
            $item->save();

            return response()->json($item, 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // 4. DEDUCT STOCK (Used When Order Food Is Placed)
    public function reduceStock(Request $request, int $id)
    {
        try {
            $validated = $request->validate([
                'deduct_quantity' => 'required|integer|min:1'
            ]);

            $item = Inventory::find($id);
            if (!$item) return response()->json(['message' => 'Item not found'], 404);

            // Check if sufficient quantity exists
            if ($item->quantity < $validated['deduct_quantity']) {
                return response()->json([
                    'message' => "Insufficient stock. Only {$item->quantity} {$item->unit} remaining."
                ], 400);
            }

            // Deduct stock
            $item->quantity -= $validated['deduct_quantity'];

            // Update status dynamically
            if ($item->quantity == 0) {
                $item->status = 'Out of Stock';
            } elseif ($item->quantity <= ($item->min_stock_level ?? 10)) {
                $item->status = 'Low Stock';
            }

            $item->save();

            return response()->json([
                'message' => 'Stock deducted successfully',
                'item'    => $item
            ], 200);

        } catch (Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // 5. DELETE ITEM
    public function destroy(int $id)
    {
        try {
            $item = Inventory::find($id);
            if (!$item) return response()->json(['message' => 'Item not found'], 404);

            $item->delete();
            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}