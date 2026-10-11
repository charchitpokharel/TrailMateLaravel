
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'TrailMate' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">TrailMate</a>
            <div class="navbar-nav">
                <a class="nav-link" href="{{ route('trails.index') }}">Trails</a>
                <a class="nav-link" href="{{ route('weather.index') }}">Weather</a>
                @auth
                    <a class="nav-link" href="{{ route('favourites.index') }}">Favourites</a>
                    <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                @else
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                    <a class="nav-link" href="{{ route('register') }}">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container py-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
