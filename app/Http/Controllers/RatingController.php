<?php

// File: app/Http/Controllers/RatingController.php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * Store a newly created rating in storage.
     */
    public function store(Request $request, Post $post)
    {
        $request->validate(['rating' => 'required|integer|between:1,5']);

        $query = $post->ratings();

        if (auth()->check()) {
            // User is logged in, check by user_id
            $query->where('user_id', auth()->id());
        } else {
            // User is anonymous, check by IP address
            $query->where('ip_address', $request->ip());
        }

        if ($query->exists()) {
            return back()->with('error', 'Anda sudah pernah memberikan rating untuk artikel ini.');
        }

        // Save the new rating
        $post->ratings()->create([
            'rating' => $request->rating,
            'user_id' => auth()->id(), // Will be null if guest
            'ip_address' => $request->ip(),
        ]);

        // Recalculate and update the post's average rating and count
        $post->refresh(); // Refresh the model to get the latest ratings
        $post->update([
            'rating' => $post->ratings()->avg('rating'),
            'ratings_count' => $post->ratings()->count(),
        ]);

        return back()->with('success', 'Terima kasih atas rating Anda!');
    }
}
