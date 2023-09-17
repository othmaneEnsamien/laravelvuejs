<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['formatted_start_time', 'formatted_end_time'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status' => AppointmentStatus::class
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function formattedStartTime(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at->format('Y-m-d h:i A'),
        );
    }

    public function formattedEndTime(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->updated_at->format('Y-m-d h:i A'),
        );
    }
}
