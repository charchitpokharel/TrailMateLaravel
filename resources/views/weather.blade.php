@extends('layouts.app', ['title' => 'Weather'])
@section('content')
<h1 class="h2">Local weather</h1><p class="muted">Use your current location to retrieve a forecast from Open-Meteo.</p><button id="locate" class="btn btn-forest">Use my location</button><div id="weatherResult" class="mt-3" aria-live="polite"></div>
@endsection
@push('scripts')
<script>
const out=document.getElementById('weatherResult');document.getElementById('locate').addEventListener('click',()=>{if(!navigator.geolocation){out.textContent='Geolocation is not supported by this browser.';return;}out.textContent='Requesting location permission…';navigator.geolocation.getCurrentPosition(async p=>{out.textContent='Loading forecast…';try{const r=await fetch(@json(route('api.weather'))+'?lat='+p.coords.latitude+'&lon='+p.coords.longitude,{headers:{'Accept':'application/json'}});const d=await r.json();if(!r.ok)throw Error(d.error||'Weather unavailable');const c=d.current;out.innerHTML=`<div class="card card-body"><h2 class="h4">Current conditions</h2><div class="display-4">${Number(c.temperature_2m).toFixed(1)} °C</div><p class="mb-1">Feels like ${Number(c.apparent_temperature).toFixed(1)} °C</p><p class="mb-1">Wind: ${Number(c.wind_speed_10m).toFixed(1)} km/h</p><p class="mb-1">Humidity: ${Number(c.relative_humidity_2m)}%</p><p class="mb-0">Precipitation: ${Number(c.precipitation)} mm</p></div>`;}catch(e){out.textContent=e.message;}},e=>{out.textContent=e.code===1?'Location permission was denied. You can enable it in browser settings.':'Unable to determine your location. Please try again.';},{timeout:10000,maximumAge:300000});});
</script>
@endpush
