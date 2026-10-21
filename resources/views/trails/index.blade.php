@extends('layouts.app', ['title' => 'Explore trails'])
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3"><div><h1 class="h2">Explore trails</h1><p class="muted">Search, filter and explore routes. Results update without a page reload.</p></div>
@auth <a class="btn btn-forest" href="{{ route('trails.create') }}">+ Add trail</a>
@else <a class="btn btn-forest" href="{{ route('login') }}">Log in to add a trail</a> @endauth
</div>
<div class="row g-2 mb-3">
 <div class="col-md-8"><label class="visually-hidden" for="trailSearch">Search trails</label><input id="trailSearch" class="form-control form-control-lg" placeholder="Search by trail, location…" autocomplete="off"></div>
 <div class="col-md-4"><label class="visually-hidden" for="difficultyFilter">Filter difficulty</label><select id="difficultyFilter" class="form-select form-select-lg"><option value="">All difficulties</option><option>Easy</option><option>Moderate</option><option>Hard</option></select></div>
</div>
<p id="resultCount" class="muted small" aria-live="polite">{{ $trails->total() }} trails</p>
<div id="trailResults" class="row g-3">
@forelse($trails as $trail)
<div class="col-sm-6 col-lg-4" data-item-card><div class="card h-100"><div class="card-body d-flex flex-column"><span class="badge text-bg-success align-self-start">{{ $trail->difficulty }}</span><h2 class="h5 mt-2">{{ $trail->name }}</h2><p class="muted">{{ $trail->location }} · {{ $trail->distance_km }} km</p><p>{{ \Illuminate\Support\Str::limit($trail->description, 110) }}</p><div class="d-flex flex-wrap gap-2 mt-auto"><a class="btn btn-sm btn-forest" href="{{ route('trails.show', $trail) }}">View details</a>
@auth
@if((isset(Auth::user()->role) && Auth::user()->role === 'admin') || ($trail->user_id && (int)$trail->user_id === (int)Auth::id()))
<a class="btn btn-sm btn-outline-secondary" href="{{ route('trails.edit', $trail) }}">Edit</a>
<button type="button" class="btn btn-sm btn-outline-danger" data-ajax-delete data-url="{{ route('trails.destroy', $trail) }}" data-confirm="Delete this trail?">Delete</button>
@endif
@endauth
</div></div></div></div>
@empty <div class="col-12"><p class="muted">No trails have been added yet.</p></div>
@endforelse
</div>
<div id="trailPagination" class="d-flex justify-content-center gap-2 mt-4"></div>
@endsection
@push('scripts')
<script>
(() => {
const input=document.getElementById('trailSearch'),filter=document.getElementById('difficultyFilter'),results=document.getElementById('trailResults'),count=document.getElementById('resultCount'),pages=document.getElementById('trailPagination');
let timer=null, currentPage=1;
const esc=window.TrailMate.escapeHtml;
const currentUserId=@json(auth()->id());
const isAdmin=@json(auth()->check() && isset(auth()->user()->role) && auth()->user()->role === 'admin');
function render(rows){results.innerHTML=rows.length?rows.map(t=>{const canManage=isAdmin||(currentUserId!==null&&Number(t.user_id)===Number(currentUserId));return `<div class="col-sm-6 col-lg-4" data-item-card><div class="card h-100"><div class="card-body d-flex flex-column"><span class="badge text-bg-success align-self-start">${esc(t.difficulty)}</span><h2 class="h5 mt-2">${esc(t.name)}</h2><p class="muted">${esc(t.location)} · ${esc(t.distance_km)} km</p><p>${esc((t.description||'').length>110?(t.description.slice(0,107)+'…'):(t.description||''))}</p><div class="d-flex flex-wrap gap-2 mt-auto"><a class="btn btn-sm btn-forest" href="${@json(url('/trails'))}/${Number(t.id)}">View details</a>${canManage?`<a class="btn btn-sm btn-outline-secondary" href="${@json(url('/trails'))}/${Number(t.id)}/edit">Edit</a><button type="button" class="btn btn-sm btn-outline-danger" data-ajax-delete data-url="${@json(url('/trails'))}/${Number(t.id)}" data-confirm="Delete this trail?">Delete</button>`:''}</div></div></div></div>`}).join(''):'<div class="col-12"><p class="muted">No matching trails found.</p></div>'}
async function load(page=1){currentPage=page;results.classList.add('loading');const params=new URLSearchParams({q:input.value.trim(),difficulty:filter.value,page:String(page)});try{const r=await fetch(@json(route('trails.index'))+'?'+params,{headers:window.TrailMate.headers()});if(!r.ok)throw Error();const d=await r.json();render(d.data||[]);count.textContent=`${d.total} trail${d.total===1?'':'s'} found`;pages.innerHTML='';if(d.last_page>1){for(let i=1;i<=d.last_page;i++){const b=document.createElement('button');b.type='button';b.className='btn '+(i===d.current_page?'btn-forest':'btn-outline-secondary');b.textContent=i;b.setAttribute('aria-label','Page '+i);b.addEventListener('click',()=>load(i));pages.appendChild(b)}}}catch(e){results.innerHTML='<div class="col-12"><p class="text-danger">Could not load trails. Please try again.</p></div>'}finally{results.classList.remove('loading')}}
input.addEventListener('input',()=>{clearTimeout(timer);timer=setTimeout(()=>load(1),250)});filter.addEventListener('change',()=>load(1));
})();
</script>
@endpush
