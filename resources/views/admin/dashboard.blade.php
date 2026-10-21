@extends('layouts.app', ['title' => 'Admin dashboard · TrailMate'])
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
  <div><span class="badge text-bg-success mb-2">ADMIN AREA</span><h1 class="h2 mb-1">TrailMate administration</h1><p class="text-secondary mb-0">Manage trails, review community activity and monitor registered accounts.</p></div>
  <a class="btn btn-outline-secondary" href="{{ route('home') }}">View public website</a>
</div>
<div class="row g-3 mb-4">
  @foreach([['Registered users',$userCount,'people'],['Trails',$trailCount,'map'],['Reviews',$reviewCount,'chat']] as [$label,$count,$icon])
  <div class="col-12 col-md-4"><div class="card h-100 shadow-sm border-0"><div class="card-body"><div class="text-secondary small">{{ $label }}</div><div class="display-6 fw-semibold">{{ $count }}</div></div></div></div>
  @endforeach
</div>
<div class="d-flex flex-wrap gap-2 mb-4">
 <a class="btn btn-success" href="{{ route('admin.trails') }}">Manage all trails</a>
 <a class="btn btn-outline-success" href="{{ route('admin.users') }}">View users</a>
 <a class="btn btn-outline-success" href="{{ route('admin.reviews') }}">Moderate reviews</a>
</div>
<div class="row g-4">
 <div class="col-12 col-xl-6"><div class="card shadow-sm h-100"><div class="card-header bg-white fw-semibold">Recently added trails</div><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Trail</th><th>Location</th><th>Added by</th><th></th></tr></thead><tbody>
 @forelse($recentTrails as $trail)<tr><td>{{ $trail->name }}</td><td>{{ $trail->location }}</td><td>{{ $trail->user?->name ?? 'Legacy / unknown' }}</td><td><a href="{{ route('trails.edit',$trail) }}">Edit</a></td></tr>@empty<tr><td colspan="4" class="text-secondary p-3">No trails yet.</td></tr>@endforelse
 </tbody></table></div></div></div>
 <div class="col-12 col-xl-6"><div class="card shadow-sm h-100"><div class="card-header bg-white fw-semibold">Recently registered users</div><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Name</th><th>Email</th><th>Joined</th></tr></thead><tbody>
 @forelse($recentUsers as $person)<tr><td>{{ $person->name }}</td><td>{{ $person->email }}</td><td>{{ $person->created_at?->format('d M Y') }}</td></tr>@empty<tr><td colspan="3" class="text-secondary p-3">No users yet.</td></tr>@endforelse
 </tbody></table></div></div></div>
</div>
@endsection
