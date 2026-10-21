@extends('layouts.app', ['title' => $trail->name])
@section('content')
<div class="card"><div class="card-body p-4"><span class="badge text-bg-success">{{ $trail->difficulty }}</span><h1 class="mt-2">{{ $trail->name }}</h1><p class="muted">{{ $trail->location }} · {{ $trail->distance_km }} km @if($trail->user) · Added by {{ $trail->user->name }} @endif</p><p>{!! nl2br(e($trail->description)) !!}</p><div class="d-flex flex-wrap gap-2">
@auth
@if((isset(Auth::user()->role) && Auth::user()->role === 'admin') || ($trail->user_id && (int)$trail->user_id === (int)Auth::id()))
<a class="btn btn-outline-secondary" href="{{ route('trails.edit', $trail) }}">Edit trail</a>
<button type="button" class="btn btn-outline-danger" data-ajax-delete data-url="{{ route('trails.destroy', $trail) }}" data-confirm="Delete this trail and its reviews?">Delete trail</button>
@endif
@endauth
<button id="fav" class="btn btn-forest" type="button" data-url="{{ route('api.favourites.toggle',$trail) }}">♡ Save favourite</button><a class="btn btn-outline-secondary" href="{{ route('reviews.index', $trail) }}">View reviews</a></div><div id="notice" class="small mt-2" aria-live="polite"></div></div></div>
@endsection
@push('scripts')
<script>
document.getElementById('fav').addEventListener('click',async function(){const button=this,notice=document.getElementById('notice');button.disabled=true;try{const r=await fetch(button.dataset.url,{method:'POST',headers:window.TrailMate.headers()});const d=await r.json();if(!r.ok)throw Error(d.message||'Unable to update favourite');button.textContent=d.saved?'♥ Saved favourite':'♡ Save favourite';notice.textContent=d.saved?'Added to favourites.':'Removed from favourites.';}catch(e){notice.textContent=e.message;}finally{button.disabled=false;}});
</script>
@endpush
