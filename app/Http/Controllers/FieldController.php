<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Crop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Client\ConnectionException;

class FieldController extends Controller
{
    private function getWeatherData()
    {
        try {
            $response = Http::get(config('services.meteoblue.url'), [
                'lat' => Config::get('location.coordinates.latitude'),
                'lon' => Config::get('location.coordinates.longitude'),
                'asl' => 113,
                'apikey' => config('services.meteoblue.key'),
                'format' => 'json'
            ]);

            if ($response->successful()) {
                $weatherData = $response->json();
                return [
                    'temperature' => $weatherData['data_day']['temperature_instant'][0],
                    'moisture' => $weatherData['data_day']['relativehumidity_mean'][0]
                ];
            }
        } catch (ConnectionException $e) {
            // Log the error if needed
            // \Log::error('Weather API connection failed: ' . $e->getMessage());
        }

        // Return default values if API call fails or throws exception
        return [
            'temperature' => 30.0,
            'moisture' => 65.0
        ];
    }

    public function index()
    {
        $fields = Field::with(['waterLogs' => function($query) {
            $query->latest('timestamp')->limit(2);
        }, 'crop'])->get();  // Added crop eager loading here

        $weather = $this->getWeatherData();

        foreach ($fields as $field) {
            $field->update([
                'temperature' => $weather['temperature'],
                'moisture' => $weather['moisture']
            ]);
        }
        
        return response()->json($fields, 200);  // Return all fields with 200 status
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surface' => 'required|numeric|min:0',
            'crop' => 'required|string',
            'condition' => 'required|string',
            'valve_state' => 'string'
        ]);

        // Create new crop
        $crop = Crop::create([
            'name' => $validated['crop'],
            'watering_frequency' => 0,
            'growth_stage' => 'SEED'
        ]);

        // Remove crop from validated data and set crop_id, crop_name
        unset($validated['crop']);
        $validated['crop_id'] = $crop->id;
        $validated['crop_name'] = $crop->name;

        // Get weather data
        $weather = $this->getWeatherData();
        $validated = array_merge($validated, $weather);
        
        $field = Field::create($validated);
        return response()->json($field->load('crop'), 201);
    }

    public function show(Field $field)
    {
        $weather = $this->getWeatherData();
        
        $field->update([
            'temperature' => $weather['temperature'],
            'moisture' => $weather['moisture']
        ]);

        return response()->json($field->load(['waterLogs' => function($query) {
            $query->latest('timestamp')->limit(2);
        }]));
    }

    public function update(Request $request, Field $field)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'surface' => 'sometimes|numeric|min:0',
            'crop' => 'sometimes|string|exists:crops,name',
            'condition' => 'sometimes|string',
            'valve_state' => 'sometimes|string'
        ]);

        if (isset($validated['crop'])) {
            $crop = Crop::where('name', $validated['crop'])->first();
            $validated['crop_id'] = $crop->id;
        }

        $field->update($validated);
        
        return response()->json($field->load(['waterLogs' => function($query) {
            $query->latest('timestamp')->limit(2);
        }]));
    }

    public function destroy(Field $field)
    {
        $field->delete();
        return response()->json(null, 204);
    }
}