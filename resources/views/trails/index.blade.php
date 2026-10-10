@extends('layouts.app', ['title' => 'Explore trails'])
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3"><div><h1 class="h2">Explore trails</h1><p class="muted">Search routes stored in the TrailMate database.</p></div><a class="btn btn-forest" href="{{ route('trails.create') }}">Add trail</a></div>
<input id="trailSearch" class="form-control form-control-lg mb-3" placeholder="Search by trail, location or difficulty…" autocomplete="off">
<div id="trailResults" class="row g-3">@foreach($trails as $trail)<div class="col-md-4"><div class="card h-100"><div class="card-body"><span class="badge text-bg-success">{{ $trail->difficulty }}</span><h2 class="h5 mt-2">{{ $trail->name }}</h2><p class="muted">{{ $trail->location }} · {{ $trail->distance_km }} km</p><p>{{ \Illuminate\Support\Str::limit($trail->description, 100) }}</p><a class="btn btn-sm btn-forest" href="{{ route('trails.show', $trail) }}">Details</a></div></div></div>@endforeach</div>
@endsection
@push('scripts')
<script>
const input=document.getElementById('trailSearch'),results=document.getElementById('trailResults');let timer;
const esc=s=>String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
input.addEventListener('input',()=>{clearTimeout(timer);timer=setTimeout(async()=>{if(input.value.trim().length<2){location.reload();return;}results.innerHTML='<p>Searching…</p>';try{const r=await fetch('{{ route('api.trails.search') }}?q='+encodeURIComponent(input.value));if(!r.ok)throw Error();const rows=await r.json();results.innerHTML=rows.length?rows.map(t=>`<div class="col-md-4"><div class="card h-100"><div class="card-body"><span class="badge text-bg-success">${esc(t.difficulty)}</span><h2 class="h5 mt-2">${esc(t.name)}</h2><p class="muted">${esc(t.location)} · ${esc(t.distance_km)} km</p><a class="btn btn-sm btn-forest" href="{{ url('/trails') }}/${Number(t.id)}">Details</a></div></div></div>`).join(''):'<p>No matching trails found.</p>';}catch(e){results.innerHTML='<p class="text-danger">Search is temporarily unavailable.</p>'}},250)});
</script>
@endpush
