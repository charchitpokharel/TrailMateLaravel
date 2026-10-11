@extends('layouts.trailmate', ['title' => $trail->name])
@section('content')
<div class="card"><div class="card-body p-4"><span class="badge text-bg-success">{{ $trail->difficulty }}</span><h1 class="mt-2">{{ $trail->name }}</h1><p class="muted">{{ $trail->location }} · {{ $trail->distance_km }} km</p><p>{!! nl2br(e($trail->description)) !!}</p><div class="d-flex flex-wrap gap-2">
<a class="btn btn-outline-secondary" href="{{ route('trails.edit', $trail) }}">Edit</a>
<form method="post" action="{{ route('trails.destroy', $trail) }}" onsubmit="return confirm('Delete this trail?')">@csrf @method('DELETE')<button class="btn btn-outline-danger">Delete</button></form>
<button id="fav" class="btn btn-forest">♡ Save favourite</button><a class="btn btn-outline-secondary" href="{{ route('reviews.index', $trail) }}">View reviews</a></div><div id="notice" class="small mt-2" aria-live="polite"></div></div></div>
@endsection
@push('scripts')
<script>
document.getElementById('fav').addEventListener('click',async()=>{try{const r=await fetch(@json(route('api.favourites.toggle',$trail)),{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'}});const d=await r.json();if(!r.ok)throw Error(d.message||d.error||'Unable to update favourite');document.getElementById('fav').textContent=d.saved?'♥ Saved':'♡ Save favourite';document.getElementById('notice').textContent='Favourite updated.';}catch(e){document.getElementById('notice').textContent=e.message;}});
</script>
@endpush
