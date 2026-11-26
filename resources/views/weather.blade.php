@extends('layouts.app', ['title' => 'Weather'])
@section('content')
<h1 class="h2">Local weather</h1>
<p class="muted">Search for a town or city, or use your current location to retrieve a forecast from Open-Meteo.</p>
<form id="weatherSearch" class="mb-3" role="search">
    <label for="weatherLocation" class="form-label">Search location</label>
    <div class="input-group">
        <input id="weatherLocation" class="form-control" type="search" placeholder="e.g. Blackpool, London or Kathmandu" minlength="2" maxlength="100" required>
        <button type="submit" class="btn btn-forest">Search</button>
    </div>
</form>
<div id="locationResults" class="list-group mb-3" aria-label="Matching locations"></div>
<button id="locate" type="button" class="btn btn-forest">Use my location</button>
<div id="weatherResult" class="mt-3" aria-live="polite"></div>
@endsection
@push('scripts')
<script>
(() => {
    const out = document.getElementById('weatherResult');
    const results = document.getElementById('locationResults');
    const input = document.getElementById('weatherLocation');
    const searchUrl = @json(route('api.weather.locations', [], false));
    const forecastUrl = @json(route('api.weather', [], false));
    let requestId = 0;

    async function getJson(url, params) {
        const response = await fetch(url + '?' + new URLSearchParams(params), {
            headers: {'Accept': 'application/json'}
        });
        const data = await response.json();
        if (!response.ok) throw new Error(data.error || data.message || 'Service unavailable. Please try again.');
        return data;
    }

    async function forecast(lat, lon, label, id) {
        out.textContent = 'Loading forecast…';
        try {
            const data = await getJson(forecastUrl, {lat, lon});
            if (id !== requestId) return;
            const current = data.current;
            if (!current || current.temperature_2m == null) throw new Error('Weather data is unavailable.');
            const card = document.createElement('div');
            card.className = 'card card-body';
            const add = (tag, cls, value) => {
                const element = document.createElement(tag);
                element.className = cls;
                element.textContent = value;
                card.append(element);
            };
            const fmt = (value, digits = 1) => value == null || !Number.isFinite(Number(value)) ? 'Unavailable' : Number(value).toFixed(digits);
            const metric = (value, unit, digits = 1) => value == null || !Number.isFinite(Number(value)) ? 'Unavailable' : `${fmt(value, digits)} ${unit}`;
            add('h2', 'h4', label);
            add('p', 'muted mb-1', 'Current conditions');
            add('div', 'display-4', metric(current.temperature_2m, '°C'));
            add('p', 'mb-1', 'Feels like: ' + metric(current.apparent_temperature, '°C'));
            add('p', 'mb-1', 'Wind: ' + metric(current.wind_speed_10m, 'km/h'));
            add('p', 'mb-1', 'Humidity: ' + metric(current.relative_humidity_2m, '%', 0));
            add('p', 'mb-0', 'Precipitation: ' + metric(current.precipitation, 'mm'));
            out.replaceChildren(card);
        } catch (error) {
            if (id === requestId) out.textContent = error.message || 'Unable to load weather.';
        }
    }

    document.getElementById('weatherSearch').addEventListener('submit', async event => {
        event.preventDefault();
        const id = ++requestId;
        const q = input.value.trim();
        results.replaceChildren();
        if (q.length < 2) { out.textContent = 'Enter at least two characters.'; return; }
        out.textContent = 'Searching locations…';
        try {
            const data = await getJson(searchUrl, {q});
            if (id !== requestId) return;
            const places = (data.results || []).filter(p => p.latitude != null && p.longitude != null && p.name);
            if (!places.length) { out.textContent = 'No matching locations found. Try another town or city.'; return; }
            out.textContent = 'Choose a location to view its weather.';
            for (const place of places) {
                const label = [...new Set([place.name, place.admin1, place.country].filter(Boolean))].join(', ');
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'list-group-item list-group-item-action';
                button.textContent = label;
                button.addEventListener('click', () => {
                    const forecastId = ++requestId;
                    input.value = place.name;
                    results.replaceChildren();
                    forecast(place.latitude, place.longitude, label, forecastId);
                });
                results.append(button);
            }
        } catch (error) {
            if (id === requestId) out.textContent = error.message || 'Unable to search locations.';
        }
    });

    document.getElementById('locate').addEventListener('click', () => {
        const id = ++requestId;
        results.replaceChildren();
        if (!navigator.geolocation) { out.textContent = 'Geolocation is not supported by this browser.'; return; }
        out.textContent = 'Requesting location permission…';
        navigator.geolocation.getCurrentPosition(position => {
            if (id === requestId) forecast(position.coords.latitude, position.coords.longitude, 'Your current location', id);
        }, error => {
            if (id === requestId) out.textContent = error.code === 1 ? 'Location permission was denied. You can search for a town instead.' : 'Unable to determine your location. Please try again.';
        }, {timeout: 10000, maximumAge: 300000});
    });
})();
</script>
@endpush
