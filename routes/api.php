<?php

use App\Http\Controllers\FieldController;
use App\Http\Controllers\WateringLogController;
use App\Http\Controllers\CropController;
use Illuminate\Support\Facades\Route;

Route::apiResource('fields', FieldController::class);
Route::apiResource('watering-logs', WateringLogController::class);
Route::apiResource('crop', CropController::class);
