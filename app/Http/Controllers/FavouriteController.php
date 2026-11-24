<?php

namespace App\Http\Controllers;

use App\Models\Trail;

class FavouriteController extends Controller
{
    public function index()
    {
        $ids = session()->get('favourites', []);
        $trails = empty($ids) ? collect() : Trail::whereIn('id', $ids)->get();
        // Remove favourites whose trails no longer exist.
        $existingIds = array_values(array_intersect($ids, $trails->pluck('id')->all()));

        if ($ids !== $existingIds) {
            session()->put('favourites', $existingIds);
        }

        return view('favourites', compact('trails'));
    }

    public function toggle(Trail $trail)
    {
        $ids = session()->get('favourites', []);
        if (in_array($trail->id, $ids)) {
            $ids = array_values(array_diff($ids, [$trail->id]));
            $saved = false;
        } else {
            $ids[] = $trail->id;
            $saved = true;
        }
        session()->put('favourites', $ids);
        return response()->json(['saved' => $saved, 'count' => count($ids)]);
    }
}
