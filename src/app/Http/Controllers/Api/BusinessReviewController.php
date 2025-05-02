<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessReviewController extends Controller
{
    public function index()
    {
        $reviews = BusinessReview::latest()->paginate(10);
        return response()->json($reviews);
    }

    public function store(Request $request)
    {
        $request->validate([
            'business_id' => 'required|exists:business_profiles,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);

        $review = BusinessReview::where('user_id', Auth::id())
            ->where('business_id', $request->business_id)
            ->first();

        if (!$review) {
            $review = BusinessReview::create([
                'business_id' => $request->business_id,
                'user_id'     => Auth::id(),
                'rating'      => $request->rating,
                'review'      => $request->review,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Review submitted successfully.',
                'data'    => $review,
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted a review for this business.',
                'data'    => $review,
            ], 409);
        }
    }

    public function show($id)
    {
        $review = BusinessReview::where('id', $id)->firstOrFail();
        return response()->json($review);
    }

    public function update(Request $request, $business_id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);

        $review = BusinessReview::where('user_id', Auth::id())
            ->where('business_id', $business_id)
            ->first();

        if (! $review) {
            return response()->json([
                'success' => false,
                'message' => 'No existing review found for this business.',
            ], 404);
        }

        $review->update([
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully.',
            'data' => $review,
        ]);
    }

    public function destroy($id)
    {
        $review = BusinessReview::findOrFail($id);

        if (Auth::id() !== $review->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully.',
        ]);
    }
}
