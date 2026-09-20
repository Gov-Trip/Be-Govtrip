<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefCity extends Model
{
    use HasFactory;

    protected $fillable = [
        'province_name',
        'city_name',
    ];

    public function travelPolicies()
    {
        return $this->hasMany(TravelPolicy::class, 'destination_city_id');
    }

    public function originTravelRequests()
    {
        return $this->hasMany(TravelRequest::class, 'origin_city_id');
    }

    public function destinationTravelRequests()
    {
        return $this->hasMany(TravelRequest::class, 'destination_city_id');
    }
}