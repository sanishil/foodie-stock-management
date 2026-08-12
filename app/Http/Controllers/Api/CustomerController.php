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
    public function index()
    {
        try {
            return response()->json(Customer::orderBy('id', 'desc')->get(), 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_name' => 'required|string|max:255',
                'customer_email' => [
                    'required',
                    'email',
                    'unique:customers,customer_email',
                    'unique:users,email'
                ],
                'phone' => 'required|string|max:20',
                'role' => 'required|string|max:50',
                'password' => 'required|string|min:4',
            ]);

            $lastCustomer = Customer::latest('id')->first();
            if ($lastCustomer && !empty($lastCustomer->customer_id)) {
                $number = (int) str_replace('CID', '', $lastCustomer->customer_id);
                $validated['customer_id'] = 'CID' . ($number + 1);
            } else {
                $validated['customer_id'] = 'CID1';
            }

            $customer = Customer::create($validated);

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
            return response()->json(['message' => 'Validation Error', 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $customer = Customer::where('id', $id)->orWhere('customer_id', $id)->first();
        
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return response()->json([
            'id' => $customer->id,
            'customer_id' => $customer->customer_id,
            'name' => $customer->customer_name, 
            'email' => $customer->customer_email,
            'phone' => $customer->phone,
            'address' => $customer->address,
            'membership' => $customer->membership ?? 'Standard',
            'avatar_url' => $customer->photo ? asset('storage/' . $customer->photo) : null,
            'role' => $customer->role
        ], 200);
    }

    // 4. UPDATE CUSTOMER PROFILE (Fixed for CID support)
    public function update(Request $request, $id)
{
    try {
        $customer = Customer::where('id', $id)->orWhere('customer_id', $id)->first();
        if (!$customer) return response()->json(['message' => 'Customer not found'], 404);

        // Input ko direct array mein le rahe hain
        $input = $request->all();

        // Database mapping (Frontend name vs Backend column)
        if (isset($input['name'])) $customer->customer_name = $input['name'];
        if (isset($input['email'])) $customer->customer_email = $input['email'];
        if (isset($input['phone'])) $customer->phone = $input['phone'];
        if (isset($input['address'])) $customer->address = $input['address'];
        if (isset($input['membership'])) $customer->membership = $input['membership'];

        $customer->save();

        return response()->json(['message' => 'Success', 'data' => $customer], 200);
    } catch (Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }
}

    public function uploadcustomerimage(Request $request, $id)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048', 
        ]);

        $customer = Customer::where('id', $id)->orWhere('customer_id', $id)->first();
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $path = $request->file('avatar')->store('customers', 'public');
        $customer->photo = $path;
        $customer->save();

        return response()->json([
            'success' => true,
            'message' => 'Customer photo updated successfully.',
            'avatar_url' => asset('storage/' . $path)
        ], 200);
    }

    public function changePassword(Request $request, $id)
    {
        $customer = Customer::where('id', $id)->orWhere('customer_id', $id)->first();
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        if ($request->current_password !== $customer->password && !Hash::check($request->current_password, $customer->password)) {
            return response()->json(['message' => 'Current password does not match'], 400);
        }

        $customer->update([
            'password' => Hash::make($request->new_password)
        ]);

        User::where('customer_id', $customer->customer_id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json(['message' => 'Password changed successfully'], 200);
    }

    public function destroy($id)
    {
        $customer = Customer::where('id', $id)->orWhere('customer_id', $id)->first();
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        User::where('customer_id', $customer->customer_id)->delete();
        $customer->delete();

        return response()->json(['message' => 'Account deleted successfully'], 200);
    }
}