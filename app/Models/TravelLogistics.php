<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelLogistics extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_request_id',
        'admin_travel_id',
        'transport_type',
        'booking_code_or_ticket',
        'hotel_name',
        'departure_datetime',
        'return_datetime',
    ];

    protected function casts(): array
    {
        return [
            'departure_datetime' => 'datetime',
            'return_datetime'    => 'datetime',
        ];
    }

    public function travelRequest()
    {
        return $this->belongsTo(TravelRequest::class);
    }

    public function adminTravel()
    {
        return $this->belongsTo(Employee::class, 'admin_travel_id');
    }
}