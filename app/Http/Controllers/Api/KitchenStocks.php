<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kitchenstock;
use Illuminate\Validation\ValidationException;

use Exception;
class KitchenStocks extends Controller
{
    public function index()
    {
        try {
            $kitchenstock = Kitchenstock::orderBy('id', 'asc')->get();
            return response()->json($kitchenstock, 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'No Data Found', 'error' => $e->getMessage()], 500);
        }
    }
    // 2. CREATE NEW kitchen stock
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'eid' => 'required|string',
                'ingredient_name' => 'required|string|max:255',
                'quantity' => 'required|integer|min:0',
                'unit' => 'required|string|max:50',
                'minimum_stock_alert' => 'required|integer|min:0',
                'status' => 'required|string|max:50',
                'user' => 'required|string|max:255',
            ]);
            $validated['request_item'] = null;
            $validated['request_to_admin'] = null;
            $kitchenstock = Kitchenstock::create($validated);

            return response()->json([
                'message' => 'Kitchen stock created successfully.',
                'kitchenstock' => $kitchenstock,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors(),
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Server Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    // 3. UPDATE EMPLOYEE DETAILS OR STATUS
    public function update(Request $request, int $id)
{
    try {
        $kitchenstock = Kitchenstock::find($id);

        if (!$kitchenstock) {
            return response()->json([
                'message' => 'Kitchen Stock not found'
            ], 404);
        }

        $validated = $request->validate([
            'eid' => 'required|string|max:50',
            'ingredient_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'minimum_stock_alert' => 'required|integer|min:0',
            'request_item' => 'nullable|numeric|min:0',
            'request_to_admin' => 'nullable|string',
            'user' => 'required|string|max:255',
        ]);

        // If frontend sends Pending, change status automatically
        if (isset($validated['request_to_admin']) && $validated['request_to_admin'] === 'Pending') {
            $validated['status'] = 'Already Requested';
        }

        $kitchenstock->update($validated);

        return response()->json([
            'message' => 'Kitchen Stock updated successfully.',
            'kitchenstock' => $kitchenstock,
        ], 200);

    } catch (ValidationException $e) {
        return response()->json([
            'message' => 'Validation Error',
            'errors' => $e->errors(),
        ], 422);

    } catch (Exception $e) {
        return response()->json([
            'message' => 'Server Error: ' . $e->getMessage(),
        ], 500);
    }
}
}
