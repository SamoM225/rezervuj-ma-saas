<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkerAvailability extends Model
{
    use BelongsToTenant, HasFactory;

    protected $table = 'worker_availability';

    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'days_of_week',
        'start_time',
        'end_time',
        'repeat_until_end_of_year',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'days_of_week' => 'array',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'repeat_until_end_of_year' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function worker()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Check if worker is available on specific date and time
    public function isAvailableAt($date, $time)
    {
        if (! $this->is_active) {
            return false;
        }

        $checkDate = Carbon::parse($date);
        $checkTime = Carbon::parse($time);

        // Check if date is within range
        if ($checkDate->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $checkDate->gt($this->end_date)) {
            return false;
        }

        // Check if day of week matches
        $dayOfWeek = $checkDate->dayOfWeek; // 0=Sunday, 1=Monday...
        if (! in_array($dayOfWeek, $this->days_of_week)) {
            return false;
        }

        // Check if time is within range
        $startTime = Carbon::parse($this->start_time);
        $endTime = Carbon::parse($this->end_time);

        return $checkTime->between($startTime, $endTime);
    }

    // Get formatted days of week for display
    public function getDaysOfWeekFormattedAttribute()
    {
        $days = ['Nedeľa', 'Pondelok', 'Utorok', 'Streda', 'Štvrtok', 'Piatok', 'Sobota'];

        return collect($this->days_of_week)->map(fn ($day) => $days[$day] ?? '')->implode(', ');
    }
}
