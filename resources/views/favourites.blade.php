@extends('layouts.trailmate', ['title' => 'My favourites'])
@section('content')
<h1 class="h2 mb-3">My favourites</h1><div class="row g-3">
@forelse($trails as $trail)<div class="col-md-4"><div class="card h-100"><div class="card-body"><h2 class="h5">{{ $trail->name }}</h2><p class="muted">{{ $trail->location }} · {{ $trail->distance_km }} km</p><a class="btn btn-forest" href="{{ route('trails.show', $trail) }}">View trail</a></div></div></div>
@empty<p class="muted">No favourites yet. Save trails from their detail pages.</p>@endforelse
</div>
@endsection
