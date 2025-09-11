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
    public function index(Request $request)
    {
        // Get all non-admin users, with optional search and pagination
        $perPage = max(1, (int) $request->get('per_page', 20));
        $search = trim((string) $request->get('q', ''));

        $customers = User::where('is_admin', false)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->withCount('orders')
            ->latest()
            ->paginate($perPage)
            ->withQueryString(); // Preserve query params across pagination

        return view('admin::customers.index', compact('customers'));
    }

    /**
     * Display the specified customer's details and order history.
     */
    public function show(User $customer)
    {
        // Eager load the user's orders and the products within those orders
        $customer->load(['orders.products']);

        return view('admin::customers.show', ['customer' => $customer]);
    }

    public function edit(User $customer)
    {
        return view('admin::customers.edit', ['customer' => $customer]);
    }
    public function update(Request $request, User $customer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $customer->id, // Ensure email is unique, except for this user
        ]);

        $customer->update($data);

        // Return JSON response for API calls
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Customer updated successfully',
                'data' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'updated_at' => $customer->updated_at
                ]
            ]);
        }

        return redirect()->route('admin.customers.show', $customer)->with('success', 'Customer updated successfully.');
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
