<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelAssignee extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_request_id',
        'employee_id',
        'is_leader',
    ];

    protected function casts(): array
    {
        return [
            'is_leader' => 'boolean',
        ];
    }

    public function travelRequest()
    {
        return $this->belongsTo(TravelRequest::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}