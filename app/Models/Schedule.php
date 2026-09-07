<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'status',
        'type',
        'title',
        'notes',
        'city',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'string',
        'end_time' => 'string',
        'notes' => 'string',
    ];

    /**
     * Owning worker of the schedule or block entry.
     */
    public function worker()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Admin user that created the manual block event.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function setStartTimeAttribute(string $value): void
    {
        $this->attributes['start_time'] = Carbon::parse($value)->format('H:i:s');
    }

    public function setDateAttribute(string $value): void
    {
        $this->attributes['date'] = Carbon::parse($value)->toDateString();
    }

    public function setEndTimeAttribute(string $value): void
    {
        $this->attributes['end_time'] = Carbon::parse($value)->format('H:i:s');
    }
}
