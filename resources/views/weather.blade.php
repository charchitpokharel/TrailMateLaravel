@extends('layouts.trailmate', ['title' => 'Local Weather'])

@section('content')
<div class="container">
    <h1 class="h2">Local weather</h1>

    <p class="text-muted">
        Allow location access to retrieve current weather conditions
        for your location.
    </p>

    <button type="button" id="locate" class="btn btn-forest">
        Use my location
    </button>

    <div id="weatherResult"
         class="mt-4"
         aria-live="polite"
         role="status"></div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const button = document.getElementById('locate');
    const output = document.getElementById('weatherResult');

    if (!button || !output) {
        return;
    }

    button.addEventListener('click', function () {
        if (!navigator.geolocation) {
            output.textContent =
                'Your browser does not support location access.';
            return;
        }

        button.disabled = true;
        output.textContent =
            'Requesting your location. Please allow access if prompted…';

        navigator.geolocation.getCurrentPosition(
            async function (position) {
                output.textContent = 'Loading your weather forecast…';

                try {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;

                    const url = @json(route('api.weather'))
                        + '?lat=' + encodeURIComponent(lat)
                        + '&lon=' + encodeURIComponent(lon);

                    const response = await fetch(url, {
                        headers: { 'Accept': 'application/json' }
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(
                            data.error || 'Weather service is unavailable.'
                        );
                    }

                    if (!data.current) {
                        throw new Error('Weather data was not returned.');
                    }

                    const current = data.current;

                    const card = document.createElement('div');
                    card.className = 'card card-body';

                    const heading = document.createElement('h2');
                    heading.className = 'h4';
                    heading.textContent = 'Current conditions';

                    const temperature = document.createElement('p');
                    temperature.className = 'display-4';
                    temperature.textContent =
                        Number(current.temperature_2m).toFixed(1) + ' °C';

                    const details = document.createElement('div');
                    details.className = 'text-muted';

                    details.textContent =
                        'Feels like ' +
                        Number(current.apparent_temperature).toFixed(1) +
                        ' °C | Wind: ' +
                        Number(current.wind_speed_10m).toFixed(1) +
                        ' km/h | Humidity: ' +
                        Number(current.relative_humidity_2m) +
                        '% | Precipitation: ' +
                        Number(current.precipitation) + ' mm';

                    card.append(heading, temperature, details);
                    output.replaceChildren(card);
                } catch (error) {
                    output.textContent =
                        error.message || 'Unable to load the forecast.';
                } finally {
                    button.disabled = false;
                }
            },
            function (error) {
                if (error.code === 1) {
                    output.textContent =
                        'Location permission was denied. Allow location ' +
                        'access for this site in your browser settings, ' +
                        'then try again.';
                } else if (error.code === 2) {
                    output.textContent =
                        'Your location could not be determined. Check ' +
                        'your device location settings and try again.';
                } else if (error.code === 3) {
                    output.textContent =
                        'Location request timed out. Please try again.';
                } else {
                    output.textContent =
                        'Unable to access your location. Please try again.';
                }

                button.disabled = false;
            },
            {
                enableHighAccuracy: false,
                timeout: 15000,
                maximumAge: 300000
            }
        );
    });
});
</script>
@endpush
