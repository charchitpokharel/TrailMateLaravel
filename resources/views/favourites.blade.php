@extends('layouts.app', ['title' => 'My favourites'])
@section('content')
<h1 class="h2 mb-3">My favourites</h1>
<div class="row g-3">
@forelse($trails as $trail)
<div class="col-md-4"><div class="card h-100"><div class="card-body">
<h2 class="h5">{{ $trail->name }}</h2>
<p class="muted">{{ $trail->location }} · {{ $trail->distance_km }} km</p>
<div class="d-flex flex-wrap gap-2">
<a class="btn btn-forest" href="{{ route('trails.show', $trail) }}">View trail</a>
@auth
@if($trail->canBeManagedBy(auth()->user()))
<a class="btn btn-outline-secondary" href="{{ route('trails.edit', $trail) }}">Edit trail</a>
@endif
@endauth
</div>
</div></div></div>
@empty
<p class="muted">No favourites yet. Save trails from their detail pages.</p>
@endforelse
</div>
@endsection
