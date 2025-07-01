<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Field extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'surface',
        'crop_id',
        'crop',
        'moisture',
        'temperature',
        'condition',
        'valve_state'
    ];

    public function waterLogs(): HasMany
    {
        return $this->hasMany(WateringLog::class);
    }

    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }
}