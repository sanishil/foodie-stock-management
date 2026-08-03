<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Exception;

class CustomerController extends Controller
{
    // Fetch Data
     public function index()
    {
        try {
            return response()->json(Customer::orderBy('id', 'desc')->get(), 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    // 2. CREATE NEW Customer
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_name' => 'required|string|max:255',
                'customer_email' => [
                    'required',
                    'email',
                    'unique:customers,customer_email', // 👈 Fixed column name
                    'unique:users,email'
                ],
                'phone' => 'required|string|max:20',
                'role' => 'required|string|max:50',
                'password' => 'required|string|min:4',
            ]);

            // Generate CID / Customer ID
            $lastCustomer = Customer::latest('id')->first();

            if ($lastCustomer && !empty($lastCustomer->customer_id)) {
                $number = (int) str_replace('CID', '', $lastCustomer->customer_id);
                $validated['customer_id'] = 'CID' . ($number + 1);
            } else {
                $validated['customer_id'] = 'CID1';
            }

            // Create Customer Record
            $customer = Customer::create($validated);

            // Create User Login Record
            $user = User::create([
                'customer_id' => $validated['customer_id'],                
                'name' => $validated['customer_name'],
                'email' => $validated['customer_email'],
                'role' => $validated['role'],
                'password' => Hash::make($validated['password']),
            ]);

            return response()->json([
                'message' => 'Customer created successfully.',
                'customer' => $customer,
                'user' => $user,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }
}