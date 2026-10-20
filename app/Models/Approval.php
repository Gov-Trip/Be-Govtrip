<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_request_id',
        'approver_employee_id',
        'delegated_from_id',
        'status',
        'notes',
        'action_at',
    ];

    protected function casts(): array
    {
        return [
            'action_at' => 'datetime',
        ];
    }

    public function travelRequest()
    {
        return $this->belongsTo(TravelRequest::class);
    }

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approver_employee_id');
    }

    public function delegatedFrom()
    {
        return $this->belongsTo(Employee::class, 'delegated_from_id');
    }
}
