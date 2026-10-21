<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Trail;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'userCount' => User::count(),
            'trailCount' => Trail::count(),
            'reviewCount' => Review::count(),
            'recentUsers' => User::query()->latest()->take(8)->get(['id', 'name', 'email', 'created_at']),
            'recentTrails' => Trail::query()->with('user:id,name')->latest()->take(8)->get(),
            'recentReviews' => Review::query()->with('trail:id,name')->latest()->take(6)->get(),
        ]);
    }

    public function users(): View
    {
        return view('admin.users', ['users' => User::query()->latest()->paginate(20)]);
    }

    public function trails(): View
    {
        return view('admin.trails', ['trails' => Trail::query()->with('user:id,name')->latest()->paginate(20)]);
    }

    public function reviews(): View
    {
        return view('admin.reviews', ['reviews' => Review::query()->with('trail:id,name')->latest()->paginate(20)]);
    }

    public function destroyReview(Review $review)
    {
        $review->delete();
        return redirect()->route('admin.reviews')->with('success', 'Review deleted.');
    }
}
