@extends('layouts.app', ['title' => $trail ? 'Edit trail' : 'Add a trail'])
@section('content')
<h1 class="h2 mb-3">{{ $trail ? 'Edit trail' : 'Add a trail' }}</h1>
<form data-ajax-form data-form-id="trailForm" method="post" action="{{ $trail ? route('trails.update', $trail) : route('trails.store') }}" class="card card-body gap-3">
@csrf @if($trail) @method('PUT') @endif
<div><label class="form-label" for="name">Trail name</label><input id="name" class="form-control" name="name" required minlength="3" maxlength="120" value="{{ old('name', $trail->name ?? '') }}"></div>
<div><label class="form-label" for="location">Location</label><input id="location" class="form-control" name="location" required minlength="2" maxlength="120" value="{{ old('location', $trail->location ?? '') }}"></div>
<div><label class="form-label" for="difficulty">Difficulty</label><select id="difficulty" class="form-select" name="difficulty" required>@foreach(['Easy','Moderate','Hard'] as $d)<option value="{{ $d }}" @selected(old('difficulty', $trail->difficulty ?? 'Easy') === $d)>{{ $d }}</option>@endforeach</select></div>
<div><label class="form-label" for="distance">Distance (km)</label><input id="distance" type="number" min="0.1" max="999.99" step="0.01" class="form-control" name="distance_km" required value="{{ old('distance_km', $trail->distance_km ?? '') }}"></div>
<div><label class="form-label" for="description">Description</label><textarea id="description" class="form-control" name="description" rows="4" required minlength="10" maxlength="5000">{{ old('description', $trail->description ?? '') }}</textarea></div>
<div data-form-message hidden role="status" aria-live="polite"></div>
<div><button type="submit" class="btn btn-forest">{{ $trail ? 'Update trail' : 'Save trail' }}</button> <a class="btn btn-outline-secondary" href="{{ route('trails.index') }}">Cancel</a></div>
</form>
@endsection
