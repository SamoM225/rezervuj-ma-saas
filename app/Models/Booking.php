<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $table = 'bookings';

    protected $fillable = [
        'user_id',
        'service_id',
        'city_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'city',
        'date',
        'start_time',
        'end_time',
        'notes',
        'status',
        'locale',
        'reminder_sent',
        'gdpr_consent_at',
        'gdpr_policy_version',
    ];

    protected $casts = [
        'gdpr_consent_at' => 'datetime',
    ];

    public function worker()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * The location (city) the booking was made for. Named `location()` so it
     * does not clash with the legacy `city` string column.
     */
    public function location()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
