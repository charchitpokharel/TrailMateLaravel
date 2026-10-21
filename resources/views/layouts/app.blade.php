<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="description" content="Explore hiking trails, save favourites, read reviews and check local weather with TrailMate.">
<title>{{ $title ?? 'TrailMate' }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="{{ asset('css/trailmate.css') }}" rel="stylesheet">
</head><body>
<nav class="navbar navbar-expand-lg navbar-dark"><div class="container">
<a class="navbar-brand fw-bold" href="{{ route('home') }}">🌿 TrailMate</a>
<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
<div class="collapse navbar-collapse" id="nav"><div class="navbar-nav ms-auto align-items-lg-center">
<a class="nav-link" href="{{ route('trails.index') }}">Explore trails</a><a class="nav-link" href="{{ route('weather') }}">Weather</a><a class="nav-link" href="{{ route('favourites') }}">Favourites</a>
@auth
@if((Auth::user()->role ?? 'user') === 'admin')<a class="nav-link fw-semibold" href="{{ route('admin.dashboard') }}">Admin dashboard</a>@endif
<a class="nav-link" href="{{ route('trails.create') }}">+ Add trail</a>
<span class="navbar-text text-white-50 px-lg-2">{{ Auth::user()->name }}</span>
<form method="POST" action="{{ route('logout') }}" class="d-inline">@csrf<button class="btn btn-sm btn-outline-light ms-lg-2 my-2 my-lg-0" type="submit">Log out</button></form>
@else
<a class="nav-link" href="{{ route('login') }}">Log in</a>
<a class="btn btn-sm btn-light ms-lg-2 my-2 my-lg-0" href="{{ route('register') }}">Sign up</a>
@endauth
</div></div></div></nav>
<main class="container py-4">
<div data-global-message hidden role="status" aria-live="polite"></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('content')
</main><footer class="container py-4 text-center muted small">TrailMate · Plan thoughtfully, explore responsibly</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/trailmate.js') }}" defer></script>
@stack('scripts')
</body></html>
