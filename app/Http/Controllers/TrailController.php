<?php

namespace App\Http\Controllers;

use App\Models\Trail;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TrailController extends Controller
{
    public function index(Request $request)
    {
        $query = Trail::query()->with('user:id,name')->orderBy('name');
        if ($request->filled('q')) {
            $term = trim((string) $request->query('q'));
            $query->where(fn ($q) => $q->where('name', 'like', "%{$term}%")
                ->orWhere('location', 'like', "%{$term}%")
                ->orWhere('difficulty', 'like', "%{$term}%"));
        }
        if ($request->filled('difficulty') && in_array($request->query('difficulty'), ['Easy','Moderate','Hard'], true)) {
            $query->where('difficulty', $request->query('difficulty'));
        }
        $trails = $query->paginate(9)->withQueryString();
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'data' => $trails->items(), 'current_page' => $trails->currentPage(),
                'last_page' => $trails->lastPage(), 'total' => $trails->total(),
            ]);
        }
        return view('trails.index', compact('trails'));
    }

    public function create()
    {
        abort_unless(auth()->check(), 403);
        return view('trails.form', ['trail' => null]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user(), 403);
        $data = $this->validated($request);
        $data['user_id'] = $request->user()->id;
        $trail = Trail::create($data);
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Trail created successfully.', 'redirect' => route('trails.show', $trail), 'trail' => $trail], 201);
        }
        return redirect()->route('trails.show', $trail)->with('success', 'Trail created.');
    }

    public function show(Trail $trail)
    {
        $trail->load('user:id,name');
        return view('trails.show', compact('trail'));
    }

    public function edit(Trail $trail)
    {
        $this->authorizeManage($trail);
        return view('trails.form', compact('trail'));
    }

    public function update(Request $request, Trail $trail)
    {
        $this->authorizeManage($trail);
        $trail->update($this->validated($request));
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Trail updated successfully.', 'redirect' => route('trails.show', $trail), 'trail' => $trail]);
        }
        return redirect()->route('trails.show', $trail)->with('success', 'Trail updated.');
    }

    public function destroy(Request $request, Trail $trail)
    {
        $this->authorizeManage($trail);
        $trail->delete();
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Trail deleted successfully.', 'redirect' => route('trails.index')]);
        }
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

    private function authorizeManage(Trail $trail): void
    {
        $user = auth()->user();
        abort_unless($user, 403);
        // Admins may manage all trails; ordinary users may manage only trails they created.
        $isAdmin = isset($user->role) && $user->role === 'admin';
        abort_unless($isAdmin || ($trail->user_id && (int) $trail->user_id === (int) $user->id), 403);
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
