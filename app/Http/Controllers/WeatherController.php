<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function index()
    {
        return view('weather');
    }

    public function forecast(Request $request)
    {
        $data = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lon' => ['required', 'numeric', 'between:-180,180'],
        ]);

        try {
            $response = Http::timeout(8)->connectTimeout(4)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $data['lat'],
                'longitude' => $data['lon'],
                'current' => 'temperature_2m,relative_humidity_2m,apparent_temperature,precipitation,wind_speed_10m,weather_code',
                'daily' => 'precipitation_probability_max',
                'timezone' => 'auto',
                'forecast_days' => 1,
            ]);
            if (!$response->successful()) {
                return response()->json(['error' => 'Weather service is temporarily unavailable.'], 502);
            }
            return response()->json($response->json());
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Weather service is temporarily unavailable.'], 502);
        }
    }
}
