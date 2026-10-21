@extends('layouts.app', ['title' => 'Review moderation · TrailMate'])
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3"><div><h1 class="h2">Review moderation</h1><p class="text-secondary">Remove reviews that violate your site's community rules.</p></div><a class="btn btn-outline-secondary" href="{{ route('admin.dashboard') }}">Back to dashboard</a></div>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Trail</th><th>Reviewer</th><th>Rating</th><th>Comment</th><th>Action</th></tr></thead><tbody>
@forelse($reviews as $review)<tr><td>{{ $review->trail?->name ?? 'Deleted trail' }}</td><td>{{ $review->reviewer_name }}</td><td>{{ $review->rating }}/5</td><td>{{ \Illuminate\Support\Str::limit($review->comment,100) }}</td><td><form method="POST" action="{{ route('admin.reviews.destroy',$review) }}" onsubmit="return confirm('Delete this review?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>@empty<tr><td colspan="5" class="p-3">No reviews found.</td></tr>@endforelse
</tbody></table></div><div class="card-body">{{ $reviews->links() }}</div></div>
@endsection
