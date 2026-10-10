@extends('layouts.app', ['title' => 'Trail reviews'])
@section('content')
<h1 class="h2">Trail reviews: {{ $trail->name }}</h1><form id="reviewForm" class="card card-body gap-2">
@csrf<input type="hidden" name="trail_id" value="{{ $trail->id }}">
<input class="form-control" name="reviewer_name" placeholder="Your name" required minlength="2" maxlength="80">
<select class="form-select" name="rating"><option value="5">5 — Excellent</option><option value="4">4 — Good</option><option value="3">3 — Average</option><option value="2">2 — Difficult</option><option value="1">1 — Poor</option></select>
<textarea class="form-control" name="comment" placeholder="Your review" required minlength="3" maxlength="1000"></textarea><button class="btn btn-forest">Submit review</button></form><div id="reviews" class="mt-3" aria-live="polite"></div>
@endsection
@push('scripts')
<script>
const form=document.getElementById('reviewForm'),list=document.getElementById('reviews'),csrf=document.querySelector('meta[name="csrf-token"]').content;
const esc=s=>String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
async function load(){const r=await fetch(@json(route('reviews.index',$trail))+'?format=json',{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});const a=await r.json();list.innerHTML=a.map(x=>`<div class="card card-body mb-2"><b>${esc(x.reviewer_name)} · ${Number(x.rating)}/5</b><p class="mb-0">${esc(x.comment)}</p></div>`).join('')||'<p>No reviews yet.</p>'}
form.addEventListener('submit',async e=>{e.preventDefault();try{const r=await fetch(@json(route('api.reviews.store',$trail)),{method:'POST',headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'},body:new FormData(form)});const d=await r.json();if(!r.ok)throw Error(d.message||Object.values(d.errors||{}).flat().join(' ')||'Unable to submit review');form.reset();load();}catch(e){alert(e.message)}});load();
</script>
@endpush
