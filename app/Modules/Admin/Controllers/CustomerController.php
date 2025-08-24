<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    /**
     * Display a listing of all customers.
     */
    public function index()
    {
        // Get all users who are not admins, along with a count of their orders
        $customers = User::where('is_admin', false)
            ->withCount('orders')
            ->latest()
            ->paginate(20); // Paginate the results

        return view('admin::customers.index', compact('customers'));
    }

    /**
     * Display the specified customer's details and order history.
     */
    public function show(User $user)
    {
        // Eager load the user's orders and the products within those orders
        $user->load(['orders.products']);

        return view('admin::customers.show', ['customer' => $user]);
    }

    public function edit(User $user)
    {
        return view('admin::customers.edit', ['customer' => $user]);
    }
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id, // Ensure email is unique, except for this user
            'is_admin' => 'sometimes|boolean',
        ]);

        // Handle the is_admin checkbox
        $data['is_admin'] = $request->has('is_admin');

        $user->update($data);

        // Return JSON response for API calls
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Customer updated successfully',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_admin' => $user->is_admin,
                    'updated_at' => $user->updated_at
                ]
            ]);
        }

        return redirect()->route('admin.customers.show', $user->id)->with('success', 'Customer updated successfully.');
    }

    /**
     * API: Get customer orders
     */
    public function orders(User $customer): JsonResponse
    {
        try {
            $orders = $customer->orders()
                ->with(['products'])
                ->latest()
                ->paginate(15);

            return response()->json([
                'status' => 'success',
                'data' => $orders,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve customer orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Block a customer
     */
    public function block(User $customer): JsonResponse
    {
        try {
            // Add a 'blocked' field or status update logic here
            // For now, we'll just update a hypothetical 'status' field
            $customer->update(['status' => 'blocked']);

            return response()->json([
                'status' => 'success',
                'message' => 'Customer blocked successfully',
                'data' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'status' => 'blocked'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to block customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Unblock a customer
     */
    public function unblock(User $customer): JsonResponse
    {
        try {
            $customer->update(['status' => 'active']);

            return response()->json([
                'status' => 'success',
                'message' => 'Customer unblocked successfully',
                'data' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'status' => 'active'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to unblock customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Delete a customer
     */
    public function destroy(User $customer): JsonResponse
    {
        try {
            // Soft delete or actual delete based on your business logic
            $customer->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Customer deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}