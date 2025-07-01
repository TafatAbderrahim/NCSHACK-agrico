<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\Field;
use App\Models\WateringLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CropController extends Controller
{
    public function index()
    {
        $crops = Crop::all();
        return response()->json($crops);
    }

    private function calculateCropMetrics(Crop $crop)
    {
        // Get all fields using relationship
        $fields = $crop->fields()->pluck('id');
        
        if ($fields->isEmpty()) {
            return [
                'watering_frequency' => 0,
                'growth_stage' => 'SEED',
                'first_watering' => null
            ];
        }

        // Get all watering logs for these fields
        $wateringLogs = WateringLog::whereIn('field_id', $fields)
                        ->orderBy('start_time', 'asc')
                        ->get();

        if ($wateringLogs->isEmpty()) {
            return [
                'watering_frequency' => 0,
                'growth_stage' => 'SEED',
                'first_watering' => null
            ];
        }

        // Calculate metrics
        $firstWatering = $wateringLogs->first()->start_time;
        $daysSinceFirst = Carbon::parse($firstWatering)->diffInDays(Carbon::now());
        $totalWaterings = $wateringLogs->count();
        
        // Calculate average waterings per day
        $frequency = $daysSinceFirst > 0 ? round($totalWaterings / $daysSinceFirst, 2) : 0;

        // Determine growth stage based on days since first watering
        $growthStage = 'SEED';
        if ($daysSinceFirst > 60) {
            $growthStage = 'RIPENING';
        } elseif ($daysSinceFirst > 45) {
            $growthStage = 'FLOWERING';
        } elseif ($daysSinceFirst > 30) {
            $growthStage = 'VEGETATIVE';
        } elseif ($daysSinceFirst > 15) {
            $growthStage = 'SPROUT';
        }

        return [
            'watering_frequency' => $frequency,
            'growth_stage' => $growthStage,
        ];
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:crops',
            'soil_moisture_range' => 'required|string'
        ]);

        // Add default values
        $validated['watering_frequency'] = 0;
        $validated['growth_stage'] = 'SEED';

        $crop = Crop::create($validated);
        return response()->json($crop, 201);
    }

    public function show(Crop $crop)
    {
        // Calculate current metrics
        $metrics = $this->calculateCropMetrics($crop);
        
        // Update crop with new metrics
        $crop->update([
            'watering_frequency' => $metrics['watering_frequency'],
            'growth_stage' => $metrics['growth_stage']
        ]);

        return response()->json($crop);
    }

    public function update(Request $request, Crop $crop)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:crops,name,' . $crop->id,
            'watering_frequency' => 'sometimes|integer|min:1',
            'growth_stage' => 'sometimes|in:SEED,SPROUT,VEGETATIVE,FLOWERING,RIPENING',
            'soil_moisture_range' => 'sometimes|array',
            'soil_moisture_range.min' => 'required_with:soil_moisture_range|numeric|min:0|max:100',
            'soil_moisture_range.max' => 'required_with:soil_moisture_range|numeric|min:0|max:100|gt:soil_moisture_range.min'
        ]);

        $crop->update($validated);
        return response()->json($crop);
    }

    public function destroy(Crop $crop)
    {
        $crop->delete();
        return response()->json(null, 204);
    }
}