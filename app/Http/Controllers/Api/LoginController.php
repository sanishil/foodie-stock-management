<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Exception;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        try {
            // Validation
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            // Check if user exists (checking by email)
            $user = User::where('email', $request->email)->first();

            // If user not found, check in customer table or throw error
            if (!$user) {
                return response()->json([
                    'message' => 'Account not found! Please check your email or register.'
                ], 404);
            }

            // Verify Password
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Invalid password! Please try again.'
                ], 401);
            }

            // // Fetch related customer details
            if ($user->role === 'Admin') {

                $employee = Employee::where('email', $user->email)->first();

                if (!$employee) {
                    return response()->json([
                        'message' => 'Employee details not found.'
                    ], 404);
                }

                return response()->json([
                    'message' => 'Login successful',
                    'user' => [
                        'id' => $user->id,
                        'eid' => $user->eid,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'phone' => $employee->phone,
                    ]
                ], 200);

            } elseif ($user->role === 'Customer') {

                $customer = Customer::where('customer_email', $user->email)->first();

                if (!$customer) {
                    return response()->json([
                        'message' => 'Customer details not found.'
                    ], 404);
                }

                return response()->json([
                    'message' => 'Login successful',
                    'user' => [
                        'id' => $user->id,
                        'customer_id' => $user->customer_id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'phone' => $customer->phone,
                    ]
                ], 200);

            }
            elseif ($user->role === 'Chef') {

                $employee = Employee::where('email', $user->email)->first();

                if (!$employee) {
                    return response()->json([
                        'message' => 'Employee details not found.'
                    ], 404);
                }

                return response()->json([
                    'message' => 'Login successful',
                    'user' => [
                        'id' => $user->id,
                        'eid' => $user->eid,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'phone' => $employee->phone,
                    ]
                ], 200);

            }

            return response()->json([
                'message' => 'Invalid user role.'
            ], 403);

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