<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Exception;
use App\Models\Employee;
use  App\Models\User;

class EmployeeController extends Controller
{
    // 1. GET ALL EMPLOYEES
    public function index()
    {
        try {
            $employees = Employee::orderBy('id', 'desc')->get();
            return response()->json($employees, 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error fetching data', 'error' => $e->getMessage()], 500);
        }
    }

    // 2. CREATE NEW EMPLOYEE
    public function store(Request $request)
    {
        try {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'role'       => 'required|string|max:255',
            'email'      => 'required|email|unique:employees,email|unique:users,email',
            'phone'      => 'required|string|max:50',
            'avatar_url' => 'nullable|string',
            'status'     => 'required|string|in:Active,On Leave,Resigned,Suspended',
        ]);

        // Generate EID
        $lastEmployee = Employee::latest('id')->first();

        if ($lastEmployee) {
            $number = (int) str_replace('EID', '', $lastEmployee->eid);
            $validated['eid'] = 'EID' . ($number + 1);
        } else {
            $validated['eid'] = 'EID1';
        }

        // Create Employee
        $employee = Employee::create($validated);

        // Create User
        $user = User::create([
            'eid'      => $validated['eid'],
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'role'    => $validated['role'],
            'password' => Hash::make('1234'),
        ]);

        return response()->json([
            'message'  => 'Employee created successfully.',
            'employee' => $employee,
            'user'     => $user,
        ], 201);

    } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors'  => $e->errors()
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // 3. UPDATE EMPLOYEE DETAILS OR STATUS
    public function update(Request $request, int $id)
    {
        try {
            $employee = Employee::find($id);

            if (!$employee) {
                return response()->json(['message' => 'Employee not found'], 404);
            }

            $validated = $request->validate([
                'name'       => 'sometimes|required|string|max:255',
                'role'       => 'sometimes|required|string|max:255',
                'email'      => 'sometimes|required|email|unique:employees,email,'.$id,
                'phone'      => 'sometimes|required|string|max:50',
                'avatar_url' => 'nullable|string',
                'status'     => 'sometimes|required|string|in:Active,On Leave,Resigned,Suspended',
            ]);

            $employee->update($validated);
            return response()->json($employee, 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors'  => $e->errors()
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // 4. DELETE EMPLOYEE
    public function destroy(int $id)
    {
        try {
            $employee = Employee::find($id);

            if (!$employee) {
                return response()->json(['message' => 'Employee not found'], 404);
            }

            $employee->delete();
            return response()->json(['message' => 'Staff deleted successfully'], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }
}