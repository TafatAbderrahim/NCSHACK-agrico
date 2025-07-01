<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class WateringLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_time',
        'end_time',
        'method',
        'field_id'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($wateringLog) {
            $start = Carbon::parse($wateringLog->start_time);
            $end = Carbon::parse($wateringLog->end_time);
            $wateringLog->duration = $end->diffInMinutes($start);
        });
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }
}
