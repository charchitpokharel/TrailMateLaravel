@extends('layouts.app', ['title' => 'Trail reviews'])
@section('content')
<h1 class="h2">Reviews: {{ $trail->name }}</h1>
<form id="reviewForm" data-ajax-form data-reset-on-success="true" action="{{ route('api.reviews.store',$trail) }}" method="post" class="card card-body gap-2">
@csrf
<label class="form-label" for="reviewer_name">Your name</label><input id="reviewer_name" class="form-control" name="reviewer_name" placeholder="Your name" required minlength="2" maxlength="80">
<label class="form-label" for="rating">Rating</label><select id="rating" class="form-select" name="rating" required><option value="5">5 — Excellent</option><option value="4">4 — Good</option><option value="3">3 — Average</option><option value="2">2 — Poor</option><option value="1">1 — Very poor</option></select>
<label class="form-label" for="comment">Review</label><textarea id="comment" class="form-control" name="comment" placeholder="Share your experience" required minlength="3" maxlength="1000"></textarea>
<div data-form-message hidden role="status" aria-live="polite"></div><button class="btn btn-forest" type="submit">Submit review</button>
</form>
<h2 class="h4 mt-4">Recent reviews</h2><div id="reviews" class="mt-3" aria-live="polite">Loading reviews…</div>
@endsection
@push('scripts')
<script>
(() => {
const list=document.getElementById('reviews'),esc=window.TrailMate.escapeHtml;
async function load(){try{const r=await fetch(@json(route('reviews.index',$trail)),{headers:window.TrailMate.headers()});if(!r.ok)throw Error();const a=await r.json();list.innerHTML=a.length?a.map(x=>`<div class="card card-body mb-2"><strong>${esc(x.reviewer_name)} · ${Number(x.rating)}/5</strong><p class="mb-0 mt-1">${esc(x.comment)}</p><small class="muted">${esc(x.created_at||'')}</small></div>`).join(''):'<p class="muted">No reviews yet. Be the first to review this trail.</p>';}catch(e){list.textContent='Could not load reviews. Please refresh and try again.'}}
document.getElementById('reviewForm').addEventListener('trailmate:success',load);load();
})();
</script>
@endpush
