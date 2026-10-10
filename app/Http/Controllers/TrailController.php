<?php

namespace App\Http\Controllers;

use App\Models\Trail;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TrailController extends Controller
{
    public function index()
    {
        $trails = Trail::orderBy('name')->get();
        return view('trails.index', compact('trails'));
    }

    public function create()
    {
        return view('trails.form', ['trail' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Trail::create($data);
        return redirect()->route('trails.index')->with('success', 'Trail created.');
    }

    public function show(Trail $trail)
    {
        return view('trails.show', compact('trail'));
    }

    public function edit(Trail $trail)
    {
        return view('trails.form', compact('trail'));
    }

    public function update(Request $request, Trail $trail)
    {
        $trail->update($this->validated($request));
        return redirect()->route('trails.show', $trail)->with('success', 'Trail updated.');
    }

    public function destroy(Trail $trail)
    {
        $trail->delete();
        return redirect()->route('trails.index')->with('success', 'Trail deleted.');
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if (mb_strlen($q) < 2) return response()->json([]);
        return response()->json(
            Trail::query()
                ->where(fn ($query) => $query->where('name', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%")
                    ->orWhere('difficulty', 'like', "%{$q}%"))
                ->orderBy('name')->limit(20)->get()
        );
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'location' => ['required', 'string', 'min:2', 'max:120'],
            'difficulty' => ['required', Rule::in(['Easy', 'Moderate', 'Hard'])],
            'distance_km' => ['required', 'numeric', 'gt:0', 'lt:1000'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
        ]);
    }
}
