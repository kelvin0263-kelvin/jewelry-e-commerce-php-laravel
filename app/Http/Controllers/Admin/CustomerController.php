<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

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

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Display the specified customer's details and order history.
     */
    public function show(User $user)
    {
        // Eager load the user's orders and the products within those orders
        $user->load(['orders.products']);

        return view('admin.customers.show', ['customer' => $user]);
    }

    public function edit(User $user)
    {
        return view('admin.customers.edit', ['customer' => $user]);
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

        return redirect()->route('admin.customers.show', $user->id)->with('success', 'Customer updated successfully.');
    }
}