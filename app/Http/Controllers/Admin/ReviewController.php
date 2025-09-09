<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Review;
use App\Modules\Product\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Display a listing of reviews
     */
    public function index(Request $request)
    {
        $query = Review::with('product');

        // Filter by approval status
        if ($request->has('status')) {
            switch ($request->status) {
                case 'approved':
                    $query->where('is_approved', true);
                    break;
                case 'pending':
                    $query->where('is_approved', false);
                    break;
            }
        }

        // Filter by product
        if ($request->has('product_id') && $request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('reviewer_name', 'like', '%' . $request->search . '%')
                  ->orWhere('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        // Sort by latest first
        $query->orderBy('created_at', 'desc');

        $reviews = $query->paginate(15);

        // Get products for filter dropdown
        $products = Product::where('is_visible', true)
                          ->whereNotNull('published_at')
                          ->orderBy('name')
                          ->get();

        return view('admin.reviews.index', compact('reviews', 'products'));
    }

    /**
     * Display the specified review
     */
    public function show(Review $review)
    {
        $review->load('product');
        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Approve a review
     */
    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);
        
        return redirect()->back()->with('success', 'Review approved successfully.');
    }

    /**
     * Reject a review
     */
    public function reject(Review $review)
    {
        $review->update(['is_approved' => false]);
        
        return redirect()->back()->with('success', 'Review rejected successfully.');
    }

    /**
     * Remove the specified review
     */
    public function destroy(Review $review)
    {
        $review->delete();
        
        return redirect()->route('admin.reviews.index')->with('success', 'Review deleted successfully.');
    }
}
