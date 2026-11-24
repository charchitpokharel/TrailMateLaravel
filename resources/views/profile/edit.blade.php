@extends('layouts.app', ['title' => 'My profile'])

@section('content')
<h1 class="h2 mb-4">My profile</h1>

@if(session('status') === 'profile-updated')
    <div class="alert alert-success">Profile updated successfully.</div>
@endif

@if(session('status') === 'password-updated')
    <div class="alert alert-success">Password updated successfully.</div>
@endif

<div class="card mb-4">
    <div class="card-body">
        <h2 class="h5 mb-3">Profile Information</h2>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')

            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input id="name" name="name" type="text" required
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $user->name) }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" name="email" type="email" required
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $user->email) }}">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-forest">Save Profile</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h2 class="h5 mb-3">Change Password</h2>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            @method('PUT')

            @foreach(['current_password' => 'Current Password', 'password' => 'New Password', 'password_confirmation' => 'Confirm New Password'] as $field => $label)
                <div class="mb-3">
                    <label for="{{ $field }}" class="form-label">{{ $label }}</label>
                    <input id="{{ $field }}" name="{{ $field }}"
                           type="password" required
                           class="form-control @error($field, 'updatePassword') is-invalid @enderror">
                    @error($field, 'updatePassword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endforeach

            <button type="submit" class="btn btn-forest">Update Password</button>
        </form>
    </div>
</div>
@endsection
