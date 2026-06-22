<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * Store or update a rating (upsert).
     */
    public function store(Request $request, Article $article)
    {
        $request->validate([
            'value' => 'required|integer|min:1|max:5',
        ]);

        Rating::updateOrCreate(
            [
                'article_id' => $article->id,
                'user_id'    => auth()->id(),
            ],
            [
                'value' => $request->value,
            ]
        );

        $average = round($article->ratings()->avg('value'), 1) ?: 0;
        $count = $article->ratings()->count();
        $userRating = $request->value;

        // If AJAX request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'    => true,
                'average'    => $average,
                'count'      => $count,
                'userRating' => $userRating,
                'message'    => 'Rating berhasil disimpan!',
            ]);
        }

        return redirect()->back()->with('success', 'Rating berhasil disimpan!');
    }
}
