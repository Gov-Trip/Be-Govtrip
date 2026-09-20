<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_request_id',
        'employee_id',
        'total_advance',    
        'total_real_expense',
        'balance_amount',
        'activity_summary',
        'status',
        'finance_notes',
    ];

    protected function casts(): array
    {
        return [
            'total_advance'      => 'decimal:2',
            'total_real_expense' => 'decimal:2',
            'balance_amount'     => 'decimal:2',
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

    public function attachments()
    {
        return $this->hasMany(TravelAttachment::class);
    }
}