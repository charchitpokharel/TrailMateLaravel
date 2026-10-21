@extends('layouts.app', ['title' => 'User management · TrailMate'])
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><div><h1 class="h2">Registered users</h1><p class="text-secondary">User directory. Role changes should be made by a trusted administrator only.</p></div><a class="btn btn-outline-secondary" href="{{ route('admin.dashboard') }}">Back to dashboard</a></div>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Registered</th></tr></thead><tbody>
@forelse($users as $person)<tr><td>{{ $person->id }}</td><td>{{ $person->name }}</td><td>{{ $person->email }}</td><td><span class="badge {{ ($person->role ?? 'user') === 'admin' ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $person->role ?? 'user' }}</span></td><td>{{ $person->created_at?->format('d M Y') }}</td></tr>@empty<tr><td colspan="5" class="p-3">No users found.</td></tr>@endforelse
</tbody></table></div><div class="card-body">{{ $users->links() }}</div></div>
@endsection
