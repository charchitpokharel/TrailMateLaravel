<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $title ?? 'TrailMate' }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
:root{--forest:#174b37;--mint:#eaf4ed}body{background:#f7f9f7;color:#203129}.navbar{background:var(--forest)}.navbar-brand,.nav-link{color:#fff!important}.hero{background:linear-gradient(120deg,#174b37,#397b57);color:white;border-radius:1.25rem;padding:3rem 2rem}.card{border:0;box-shadow:0 5px 20px #193c2910;border-radius:1rem}.btn-forest{background:var(--forest);color:white}.btn-forest:hover{background:#286447;color:white}.muted{color:#64756b}@media(max-width:576px){.hero{padding:2rem 1.25rem}}
</style>
</head><body>
<nav class="navbar navbar-expand-lg navbar-dark"><div class="container">
<a class="navbar-brand fw-bold" href="{{ route('home') }}">🌿 TrailMate</a>
<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
<div class="collapse navbar-collapse" id="nav"><div class="navbar-nav ms-auto">
<a class="nav-link" href="{{ route('trails.index') }}">Explore</a><a class="nav-link" href="{{ route('weather') }}">Weather</a><a class="nav-link" href="{{ route('favourites') }}">Favourites</a>
</div></div></div></nav>
<main class="container py-4">
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('content')
</main><footer class="container py-4 text-center muted small">TrailMate · Plan thoughtfully, explore responsibly</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body></html>
