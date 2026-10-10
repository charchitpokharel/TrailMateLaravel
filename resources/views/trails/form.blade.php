@extends('layouts.app', ['title' => $trail ? 'Edit trail' : 'Add a trail'])
@section('content')
<h1 class="h2 mb-3">{{ $trail ? 'Edit trail' : 'Add a trail' }}</h1>
<form method="post" action="{{ $trail ? route('trails.update', $trail) : route('trails.store') }}" class="card card-body gap-3">
@csrf @if($trail) @method('PUT') @endif
<div><label class="form-label">Trail name</label><input class="form-control" name="name" required maxlength="120" value="{{ old('name', $trail->name ?? '') }}"></div>
<div><label class="form-label">Location</label><input class="form-control" name="location" required maxlength="120" value="{{ old('location', $trail->location ?? '') }}"></div>
<div><label class="form-label">Difficulty</label><select class="form-select" name="difficulty" required>@foreach(['Easy','Moderate','Hard'] as $d)<option value="{{ $d }}" @selected(old('difficulty', $trail->difficulty ?? 'Easy') === $d)>{{ $d }}</option>@endforeach</select></div>
<div><label class="form-label">Distance (km)</label><input type="number" min="0.1" max="999.99" step="0.01" class="form-control" name="distance_km" required value="{{ old('distance_km', $trail->distance_km ?? '') }}"></div>
<div><label class="form-label">Description</label><textarea class="form-control" name="description" rows="4" required>{{ old('description', $trail->description ?? '') }}</textarea></div>
<div><button class="btn btn-forest">Save trail</button> <a class="btn btn-outline-secondary" href="{{ route('trails.index') }}">Cancel</a></div>
</form>
@endsection
