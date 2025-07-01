<?php

namespace App\Http\Controllers;

use App\Models\WateringLog;
use App\Models\Field;
use Illuminate\Http\Request;

class WateringLogController extends Controller
{
    public function index()
    {
        $logs = WateringLog::with(['field.cropData'])->latest('timestamp')->get();
        return response()->json($logs);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'method' => 'required|string|in:SPRINKLER,DRIP,FLOOD',
            'field_id' => 'required|exists:fields,id',
        ]);

        $field = Field::findOrFail($validated['field_id']);
        $log = $field->waterLogs()->create($validated);
        
        return response()->json($log, 201);
    }

    public function show(WateringLog $wateringLog)
    {
        return response()->json($wateringLog->load('field.cropData'));
    }

    public function update(Request $request, WateringLog $wateringLog)
    {
        $validated = $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'method' => 'sometimes|string|in:SPRINKLER,DRIP,FLOOD',
        ]);

        $wateringLog->update($validated);
        return response()->json($wateringLog->load('field.cropData'));
    }

    public function destroy(WateringLog $wateringLog)
    {
        $wateringLog->delete();
        return response()->json(null, 204);
    }

    public function getFieldLogs($fieldId)
    {
        $logs = WateringLog::where('field_id', $fieldId)
            ->latest('timestamp')
            ->get();
        return response()->json($logs);
    }
}