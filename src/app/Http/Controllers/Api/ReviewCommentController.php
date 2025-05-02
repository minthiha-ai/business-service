<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ReviewComment;
use App\Models\BusinessReview;

class ReviewCommentController extends Controller
{
    /**
     * List all comments for a given review.
     */
    public function index($reviewId)
    {
        $comments = ReviewComment::where('review_id', $reviewId)
            ->where('status', true)
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Comments fetched successfully.',
            'data' => $comments
        ]);
    }

    /**
     * Store a new comment or reply on a review.
     */
    public function store(Request $request)
    {
        $request->validate([
            'review_id' => 'required|exists:business_reviews,id',
            'comment'   => 'required|string',
            'type'      => 'in:comment,reply'
        ]);

        $comment = ReviewComment::create([
            'review_id' => $request->review_id,
            'user_id'   => Auth::id(),
            'comment'   => $request->comment,
            'type'      => $request->type ?? 'comment',
            'status'    => true,
        ]);

        return response()->json([
            'message' => 'Comment created successfully.',
            'data' => $comment
        ], 201);
    }

    /**
     * Update user's own comment.
     */
    public function update(Request $request, $id)
    {
        $comment = ReviewComment::findOrFail($id);

        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'comment' => 'required|string',
        ]);

        $comment->update([
            'comment' => $request->comment
        ]);

        return response()->json([
            'message' => 'Comment updated successfully.',
            'data' => $comment
        ]);
    }

    /**
     * Delete user's own comment.
     */
    public function destroy($id)
    {
        $comment = ReviewComment::findOrFail($id);

        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully.'
        ]);
    }
}
