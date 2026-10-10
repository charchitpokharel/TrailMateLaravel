<?php

namespace App\Http\Controllers;

use App\Models\Trail;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Trail $trail)
    {
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json($trail->reviews()->latest()->get());
        }
        return view('reviews.index', compact('trail'));
    }

    public function store(Request $request, Trail $trail)
    {
        $data = $request->validate([
            'reviewer_name' => ['required', 'string', 'min:2', 'max:80'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'min:3', 'max:1000'],
        ]);
        $review = $trail->reviews()->create($data);
        return response()->json(['success' => true, 'message' => 'Review submitted.', 'review' => $review], 201);
    }
}
