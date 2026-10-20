<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'destination_city_id',
        'grade_group',
        'daily_allowance',
        'max_hotel_rate',
        'max_transport_rate',
        'effective_year',
    ];

    protected function casts(): array
    {
        return [
            'daily_allowance' => 'decimal:2',
            'max_hotel_rate' => 'decimal:2',
            'max_transport_rate' => 'decimal:2',
            'effective_year' => 'integer',
        ];
    }

    public function destinationCity()
    {
        return $this->belongsTo(RefCity::class, 'destination_city_id');
    }
}
